<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    function quicklession() {
        return view('memberpages.quicklesson');
    }

    function learnSongs() {
        return view('memberpages.learnsongs');
    }
}
