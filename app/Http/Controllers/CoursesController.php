<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;

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
    function extraCourses() {
        $all = Upload::where('category', 'extra courses')->paginate(5);
        $beginner = Upload::where('category', 'extra courses')->where('level', 'Beginner')->paginate(9);
        $intermediate = Upload::where('category', 'extra courses')->where('level', 'Intermediate')->paginate(9);
        $advanced = Upload::where('category', 'extra courses')->where('level', 'Advanced')->paginate(9);

        return view('memberpages.extracources', compact('all', 'beginner', 'intermediate', 'advanced'));
    }

    function singleCourse($id) {

        $lesson = Upload::where('id', $id)->first();

        if (!$lesson) {
            abort(404); 
        }

        $relatedUploads = Upload::where('category', $lesson->category)
                                ->where('id', '!=', $id)
                                ->get();

    return view('memberpages.singleExtracourse', compact('lesson', 'relatedUploads'));
}
}
