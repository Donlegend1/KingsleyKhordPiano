<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    

    function pianoExercise() {
        return view('memberpages.pianoexercise');
    }
}
