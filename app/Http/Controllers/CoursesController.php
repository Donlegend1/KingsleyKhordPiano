<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        return view('memberpages.extracources');
    }

    function singleCourse($id) {
        return view('memberpages.singleExtracourse');
    }
}
