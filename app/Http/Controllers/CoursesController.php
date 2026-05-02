<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Services\BookmarkService;
use App\Models\CourseVideoComment;

class CoursesController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the courses page.
     *
     * @return \Illuminate\View\View
     */
    public function extraCourses(Request $request)
    {
        $name = $request->input('name');
        $series = $request->input('series');
        $allPage = $request->input('all_page', 1);
        $beginnerPage = $request->input('beginner_page', 1);
        $intermediatePage = $request->input('intermediate_page', 1);
        $advancedPage = $request->input('advanced_page', 1);

        $fetchQuery = function($level = null) use ($name, $series) {
            $query = \App\Models\Upload::where('category', 'extra courses');
            
            if ($level) {
                $query->where('level', $level);
            }

            if ($name) {
                $query->where('title', 'like', '%' . $name . '%');
            }
            if ($series) {
                $query->where('series', $series)->orderBy('id', 'asc');
            } else {
                // Grouping logic: Get unique series or standalone items
                // We use a subquery to get the IDs of items we want to show
                $subquery = \App\Models\Upload::where('category', 'extra courses')
                    ->when($level, fn($q) => $q->where('level', $level))
                    ->when($name, fn($q) => $q->where('title', 'like', '%' . $name . '%'))
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

        return view('memberpages.extracources', compact('all', 'beginner', 'intermediate', 'advanced', 'series'));
    }

    public function singleCourse($id, BookmarkService $service) 
    {
        $lesson = Upload::findOrFail($id);

        $comments = CourseVideoComment::where('category', 'others')
            ->whereNot('course_id', $id)
            ->with(['user', 'replies.user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        $relatedUploads = collect();
        if (is_array($lesson->tags) && count($lesson->tags)) {
            $relatedUploads = Upload::whereIn('id', $lesson->tags)->get();
        }

        $isBookmarked = $service->isBookmarked($lesson);

        return view('memberpages.singleExtracourse', compact(
            'lesson',
            'relatedUploads',
            'comments',
            'isBookmarked'
        ));
    }
}
