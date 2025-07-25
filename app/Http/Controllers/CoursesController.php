<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
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
    public function extraCourses() {
        $all = Upload::where('category', 'extra courses')->paginate(9);
        $beginner = Upload::where('category', 'extra courses')->where('level', 'Beginner')->paginate(9);
        $intermediate = Upload::where('category', 'extra courses')->where('level', 'Intermediate')->paginate(9);
        $advanced = Upload::where('category', 'extra courses')->where('level', 'Advanced')->paginate(9);

        return view('memberpages.extracources', compact('all', 'beginner', 'intermediate', 'advanced'));
    }

    function singleCourse($id) {
        $lesson = Upload::findOrFail($id);
        $comments  = Course_video_comments::where('category', 'course')
            ->with(['user', 'replies.user'])
            ->latest()
            ->take(3)
            ->get();

        $relatedUploads = collect();

        if (is_array($lesson->tags) && count($lesson->tags)) {
            $relatedUploads = Upload::whereIn('id', $lesson->tags)->get();
        }

        return view('memberpages.singleExtracourse', compact('lesson', 'relatedUploads', 'comments'));
    }

}
