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
    public function quicklession() 
    {
        $all = Upload::where('category', 'quick lessons')->paginate(9);
        $beginner = Upload::where('category', 'quick lessons')->where('level', 'Beginner')->paginate(9);
        $intermediate = Upload::where('category', 'quick lessons')->where('level', 'Intermediate')->paginate(9);
        $advanced = Upload::where('category', 'quick lessons')->where('level', 'Advanced')->paginate(9);
        // dd($all);

        return view('memberpages.quicklesson', compact('all', 'beginner', 'intermediate', 'advanced'));
    }

    public function learnSongs()
    {
        $all = Upload::where('category', 'learn songs')->paginate(9);
        $beginner = Upload::where('category', 'learn songs')->where('level', 'Beginner')->paginate(9);
        $intermediate = Upload::where('category', 'learn songs')->where('level', 'Intermediate')->paginate(9);
        $advanced = Upload::where('category', 'learn songs')->where('level', 'Advanced')->paginate(9);

        return view('memberpages.learnsongs', compact('all', 'beginner', 'intermediate', 'advanced'));
    }
}
