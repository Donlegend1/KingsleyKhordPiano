<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Bookmark;
use App\Models\Course_video_comments;

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
        $allPage = $request->input('all_page', 1);
        $beginnerPage = $request->input('beginner_page', 1);
        $intermediatePage = $request->input('intermediate_page', 1);
        $advancedPage = $request->input('advanced_page', 1);

        $allQuery = \App\Models\Upload::where('category', 'extra courses')->latest();
        if ($name) {
            $allQuery->where('title', 'like', '%' . $name . '%');
        }
        $all = $allQuery->paginate(9, ['*'], 'all_page', $allPage);

        $beginnerQuery = \App\Models\Upload::where('category', 'extra courses')->where('level', 'Beginner');
        if ($name) {
            $beginnerQuery->where('title', 'like', '%' . $name . '%');
        }
        $beginner = $beginnerQuery->paginate(9, ['*'], 'beginner_page', $beginnerPage);

        $intermediateQuery = \App\Models\Upload::where('category', 'extra courses')->where('level', 'Intermediate');
        if ($name) {
            $intermediateQuery->where('title', 'like', '%' . $name . '%');
        }
        $intermediate = $intermediateQuery->paginate(9, ['*'], 'intermediate_page', $intermediatePage);

        $advancedQuery = \App\Models\Upload::where('category', 'extra courses')->where('level', 'Advanced');
        if ($name) {
            $advancedQuery->where('title', 'like', '%' . $name . '%');
        }
        $advanced = $advancedQuery->paginate(9, ['*'], 'advanced_page', $advancedPage);

        return view('memberpages.extracources', compact('all', 'beginner', 'intermediate', 'advanced'));
    }

    public function singleCourse($id) 
    {
        $lesson = Upload::findOrFail($id);

        $comments = Course_video_comments::where('category', 'course')
            ->with(['user', 'replies.user'])
            ->latest()
            ->take(3)
            ->get();

        $relatedUploads = collect();
        if (is_array($lesson->tags) && count($lesson->tags)) {
            $relatedUploads = Upload::whereIn('id', $lesson->tags)->get();
        }

        $isBookmarked = Bookmark::where('user_id', auth()->id())
            ->where('video_id', $lesson->id)
            ->where('source', 'uploads') 
            ->exists();

        return view('memberpages.singleExtracourse', compact(
            'lesson',
            'relatedUploads',
            'comments',
            'isBookmarked'
        ));
    }
}
