<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LessonController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function learnSongs(Request $request)
    {
        \App\Models\CategoryView::markViewed(auth()->id(), 'learn_songs');

        $search = $request->input('search');
        $activeTab = $request->input('tab', 'all');
        $tonalCenter = $request->input('key', 'all');
        $page = $request->input('page', 1);

        $query = \App\Models\LearnSong::query()
            ->where('status', 'active')
            ->with('category')
            ->when($activeTab !== 'all', fn($q) => $q->where('level', $activeTab))
            ->when($tonalCenter !== 'all', fn($q) => $q->where('tonal_center', $tonalCenter))
            ->when($search, function($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('title', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $songs = $query->paginate(9, ['*'], 'page', $page)->appends([
            'tab' => $activeTab,
            'key' => $tonalCenter,
            'search' => $search,
        ]);

        $tonalCenters = \App\Enums\Music\TonalCenterEnum::options();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->view('memberpages.partials.learnsongs-results', compact('songs', 'search'));
        }

        return view('memberpages.learnsongs', compact('songs', 'search', 'activeTab', 'tonalCenter', 'tonalCenters'));
    }
}
