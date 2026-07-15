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
            ->orderByRaw('position IS NULL, position ASC')
            ->latest();

        $songs = $query->paginate(9, ['*'], 'page', $page)->appends([
            'tab' => $activeTab,
            'key' => $tonalCenter,
            'search' => $search,
        ]);

        $tonalCenters = \App\Enums\Music\TonalCenterEnum::options();

        return view('memberpages.learnsongs', compact('songs', 'search', 'activeTab', 'tonalCenter', 'tonalCenters'));
    }
}
