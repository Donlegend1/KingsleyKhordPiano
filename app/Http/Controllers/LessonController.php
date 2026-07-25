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
        $levels = ['All', 'Beginner', 'Intermediate', 'Advanced'];
        $keys = \App\Enums\Music\TonalCenterEnum::options();
        $levelFilter = collect((array) $request->input('level', []))
            ->filter()
            ->values()
            ->all();
        $keyFilter = collect((array) $request->input('key', []))
            ->filter()
            ->values()
            ->all();
        $selectedLevels = collect($levelFilter)
            ->reject(fn ($level) => $level === 'All')
            ->map(fn ($level) => strtolower($level))
            ->filter(fn ($level) => in_array($level, ['beginner', 'intermediate', 'advanced'], true))
            ->values()
            ->all();

        $fetchLevelCategories = function($level) use ($search, $selectedLevels, $keyFilter) {
            $query = \App\Models\LearnSongCategory::where('level', $level)
                ->orderBy('position');

            $query->with(['songs' => function($q) use ($search, $level, $keyFilter) {
                $q->where('level', $level)
                  ->where('status', 'active')
                  ->when($keyFilter, function($subQ) use ($keyFilter) {
                      $subQ->whereIn('tonal_center', $keyFilter);
                  })
                  ->when($search, function($subQ) use ($search) {
                      $subQ->where(function ($searchQuery) use ($search) {
                          $searchQuery->where('title', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%");
                      });
                  })
                  ->with('category')
                  ->orderBy('position');
            }]);

            $categories = $query->get();

            if ($search || $keyFilter || $selectedLevels) {
                $categories = $categories->filter(function($category) {
                    return $category->songs->isNotEmpty();
                });
            }

            return $categories;
        };

        $beginnerCategories = in_array('beginner', $selectedLevels, true) || ! $selectedLevels
            ? $fetchLevelCategories('beginner')
            : collect();
        $intermediateCategories = in_array('intermediate', $selectedLevels, true) || ! $selectedLevels
            ? $fetchLevelCategories('intermediate')
            : collect();
        $advancedCategories = in_array('advanced', $selectedLevels, true) || ! $selectedLevels
            ? $fetchLevelCategories('advanced')
            : collect();

        return view('memberpages.learnsongs', compact(
            'beginnerCategories',
            'intermediateCategories',
            'advancedCategories',
            'search',
            'levelFilter',
            'levels',
            'keyFilter',
            'keys'
        ));
    }
}
