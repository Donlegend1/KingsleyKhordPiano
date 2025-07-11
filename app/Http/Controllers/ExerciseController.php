<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;

class ExerciseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function pianoExercise(Request $request)
    {
        $level = $request->query('level');
        $skillLevel = $request->query('skill_level');

        $query = Upload::query()->where('category', 'piano exercise');

        if ($level) {
            $query->where('level', $level);
        }

        if ($skillLevel) {
            $query->where('skill_level', $skillLevel);
        }

        $exercises = $query->latest()->paginate(12);

        $levels = ['independence', 'coordination', 'flexibility', 'strength', 'dexterity'];
        $skillLevels = ['Basic', 'Competent', 'Challenging'];

        return view('memberpages.pianoexercise', compact('exercises', 'level', 'skillLevel', 'levels', 'skillLevels'));
    }
}
