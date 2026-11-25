<?php

namespace App\Http\Controllers;

use App\Enums\Roles\UserRoles;
use App\Models\PianoGuideComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PianoGuideDiscussionController extends Controller
{
    public function join(string $slug)
    {
        return $this->backToDiscussion($slug);
    }

    public function store(Request $request, string $slug)
    {
        $redirect = $this->backToDiscussion($slug, $request->filled('parent_id') ? $request->integer('comments_page', 1) : 1);
        $validator = Validator::make($request->only('body', 'parent_id'), [
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $redirect->withErrors($validator, 'guideDiscussion')->withInput();
        }

        $data = $validator->validated();
        $saved = DB::transaction(function () use ($request, $slug, $data) {
            $parentId = $data['parent_id'] ?? null;

            if ($parentId !== null && !PianoGuideComment::where('guide_slug', $slug)
                ->whereNull('parent_id')->whereNotNull('body')->lockForUpdate()->find($parentId)) {
                return false;
            }

            PianoGuideComment::create([
                'guide_slug' => $slug,
                'user_id' => $request->user()->id,
                'parent_id' => $parentId,
                'body' => $data['body'],
            ]);

            return true;
        });

        if (!$saved) {
            return $redirect->withErrors(['body' => 'This comment is no longer open for replies.'], 'guideDiscussion');
        }

        return $redirect->with('guideDiscussionStatus', ($data['parent_id'] ?? null) ? 'Reply posted.' : 'Comment posted.');
    }

    public function destroy(Request $request, string $slug, string $comment)
    {
        $redirect = $this->backToDiscussion($slug, $request->integer('comments_page', 1));
        $record = PianoGuideComment::where('guide_slug', $slug)->whereNotNull('body')->findOrFail($comment);
        $user = $request->user();
        abort_unless((int) $record->user_id === (int) $user->id
            || in_array($user->role, [UserRoles::ADMIN->value, UserRoles::SUPERADMIN->value], true), 403);

        $record->update(['body' => null]);

        return $redirect->with('guideDiscussionStatus', 'Comment removed.');
    }

    private function backToDiscussion(string $slug, int $page = 1)
    {
        abort_unless(array_key_exists($slug, config('piano-guides')), 404);

        $parameters = ['slug' => $slug];
        if ($page > 1) {
            $parameters['comments_page'] = $page;
        }

        return redirect()->route('guides.show', $parameters, 303)->withFragment('guide-discussion');
    }
}
