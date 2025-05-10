<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LessonController extends Controller
{
    function quicklession() {
        return view('memberpages.quicklesson');
    }

    function learnSongs() {
        return view('memberpages.learnsongs');
    }
}
