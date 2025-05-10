<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    function pianoExercise() {
        return view('memberpages.pianoexercise');
    }
}
