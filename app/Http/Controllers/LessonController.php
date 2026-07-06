<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;

class LessonController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the lessons page.
     *
     * @return \Illuminate\View\View
     */
    public function quicklession(Request $request) 
    {
        $allPage = $request->input('all_page', 1);
        $beginnerPage = $request->input('beginner_page', 1);
        $intermediatePage = $request->input('intermediate_page', 1);
        $advancedPage = $request->input('advanced_page', 1);
        $search = $request->input('search');
        $series = $request->input('series');

        $fetchQuery = function($level = null) use ($search, $series) {
            $query = Upload::where('category', 'quick lessons');
            
            if ($level) {
                $query->where('level', $level);
            }

            if ($search) {
                $query->where('title', 'like', "%{$search}%");
            }

            if ($series) {
                $query->where('series', $series)->orderBy('id', 'asc');
            } else {
                $subquery = Upload::where('category', 'quick lessons')
                    ->when($level, fn($q) => $q->where('level', $level))
                    ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
                    ->selectRaw('MIN(id) as id')
                    ->groupBy(\DB::raw('COALESCE(series, CAST(id AS CHAR))'));
                
                $query->whereIn('id', $subquery)
                    ->select('uploads.*')
                    ->selectSub(function($q) {
                        $q->from('uploads as u2')
                          ->whereRaw('COALESCE(u2.series, CAST(u2.id AS CHAR)) = COALESCE(uploads.series, CAST(uploads.id AS CHAR))')
                          ->selectRaw('count(*)');
                    }, 'item_count')
                    ->latest();
            }

            return $query;
        };

        $all = $fetchQuery()->paginate(9, ['*'], 'all_page', $allPage);
        $beginner = $fetchQuery('Beginner')->paginate(9, ['*'], 'beginner_page', $beginnerPage);
        $intermediate = $fetchQuery('Intermediate')->paginate(9, ['*'], 'intermediate_page', $intermediatePage);
        $advanced = $fetchQuery('Advanced')->paginate(9, ['*'], 'advanced_page', $advancedPage);

        return view('memberpages.quicklesson', compact('all', 'beginner', 'intermediate', 'advanced', 'search', 'series'));
    }

    public function learnSongs(Request $request)
    {
        \App\Models\CategoryView::markViewed(auth()->id(), 'learn_songs');

        $search = $request->input('search');
        $activeTab = $request->input('tab', 'beginner');

        $fetchLevelCategories = function($level) use ($search) {
            $query = \App\Models\LearnSongCategory::where('level', $level)
                ->orderBy('position');

            $query->with(['songs' => function($q) use ($search, $level) {
                $q->where('level', $level)
                  ->where('status', 'active')
                  ->when($search, function($subQ) use ($search) {
                      $subQ->where(function($query) use ($search) {
                          $query->where('title', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%")
                                ->orWhere('author', 'like', "%{$search}%");
                      });
                  })
                  ->orderBy('position');
            }]);

            $categories = $query->get();

            if ($search) {
                $categories = $categories->filter(function($category) {
                    return $category->songs->isNotEmpty();
                });
            }

            return $categories;
        };

        $beginnerCategories = $fetchLevelCategories('beginner');
        $intermediateCategories = $fetchLevelCategories('intermediate');
        $advancedCategories = $fetchLevelCategories('advanced');

        return view('memberpages.learnsongs', compact(
            'beginnerCategories',
            'intermediateCategories',
            'advancedCategories',
            'search',
            'activeTab'
        ));
    }
}
