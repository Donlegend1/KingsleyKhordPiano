<?php

namespace App\Http\Controllers;

use App\Mail\PianoGuideMail;
use App\Models\PianoGuideComment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class PianoGuideController extends Controller
{
    public function index()
    {
        $catalog = config('piano-guides');
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 12;
        $items = array_slice($catalog, ($page - 1) * $perPage, $perPage, true);

        abort_if($page > 1 && !$items, 404);

        $guides = new LengthAwarePaginator($items, count($catalog), $perPage, $page, [
            'path' => route('guides.index'),
        ]);

        return view('guides.index', compact('guides'));
    }

    public function show(string $slug)
    {
        $catalog = config('piano-guides');
        abort_unless(isset($catalog[$slug]), 404);

        $discussion = PianoGuideComment::where('guide_slug', $slug)->whereNull('parent_id')
            ->where(fn ($query) => $query->whereNotNull('body')->orWhereHas('replies'))
            ->with(['author:id,display_name,first_name', 'replies.author:id,display_name,first_name'])
            ->orderByDesc('id')->paginate(12, ['*'], 'comments_page')
            ->fragment('guide-discussion');

        return view("guides.{$slug}", ['guide' => $catalog[$slug], 'guideSlug' => $slug, 'discussion' => $discussion]);
    }

    public function requestPdf(Request $request, string $slug)
    {
        $this->pdfPath($slug);
        $validated = $request->validate(['email' => ['required', 'string', 'email', 'max:254']]);
        $email = mb_strtolower($validated['email']);

        DB::table('piano_guide_leads')->upsert(
            ['guide_slug' => $slug, 'email' => $email, 'created_at' => now()],
            ['guide_slug', 'email'],
            ['email']
        );

        $guide = config('piano-guides')[$slug];
        $pdfUrl = URL::temporarySignedRoute('guides.pdf', now()->addDays(7), ['slug' => $slug]);

        try {
            Mail::to($email)->send(new PianoGuideMail($guide['pdf_title'] ?? $guide['title'], $pdfUrl, $guide['email_tip'] ?? null));
        } catch (\Throwable $error) {
            report($error);

            return redirect()->route('guides.show', $slug)
                ->withInput()
                ->withErrors(['email' => 'We couldn’t send the guide right now. Please try again.'])
                ->withFragment('guide-pdf');
        }

        $request->session()->put("piano_guide_sent.{$slug}", true);

        return redirect()->route('guides.confirmation', $slug, 303);
    }

    public function confirmation(Request $request, string $slug)
    {
        $this->pdfPath($slug);

        if (!$request->session()->get("piano_guide_sent.{$slug}")) {
            return redirect()->route('guides.show', $slug)->withFragment('guide-pdf');
        }

        return view('guides.confirmation', ['guide' => config('piano-guides')[$slug], 'slug' => $slug]);
    }

    public function pdf(Request $request, string $slug)
    {
        $path = $this->pdfPath($slug);

        if (!$request->hasValidSignature()) {
            return redirect()->route('guides.show', $slug)->withFragment('guide-pdf');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function pdfPath(string $slug): string
    {
        $catalog = config('piano-guides');
        abort_unless(!empty($catalog[$slug]['pdf']), 404);

        $path = resource_path($catalog[$slug]['pdf']);
        abort_unless(is_file($path), 404);

        return $path;
    }
}
