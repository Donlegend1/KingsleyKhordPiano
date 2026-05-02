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
        $search = $request->query('search');
        $series = $request->query('series');

        $query = Upload::query()->where('category', 'piano exercise');

        if ($level) {
            $query->where('level', $level);
        }

        if ($skillLevel) {
            $query->where('skill_level', $skillLevel);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($series) {
            $query->where('series', $series)->orderBy('id', 'asc');
        } else {
            $subquery = Upload::where('category', 'piano exercise')
                ->when($level, fn($q) => $q->where('level', $level))
                ->when($skillLevel, fn($q) => $q->where('skill_level', $skillLevel))
                ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
                ->selectRaw('MIN(id) as id')
                ->groupBy(\DB::raw('COALESCE(series, CAST(id AS CHAR))'));
            
            $query->whereIn('id', $subquery)
                ->select('uploads.*')
                ->selectSub(function($q) {
                    $q->from('uploads as u2')
                      ->whereRaw('COALESCE(u2.series, CAST(u2.id AS CHAR)) = COALESCE(uploads.series, CAST(uploads.id AS CHAR))')
                      ->selectRaw('count(*)');
                }, 'item_count')
                ->latest();
        }

        $exercises = $query->paginate(12);

        $levels = ['independence', 'technique', 'flexibility', 'strength', 'dexterity'];
        $skillLevels = ['Basic', 'Competent', 'Challenging'];

        return view('memberpages.pianoexercise', compact('exercises', 'level', 'skillLevel', 'search', 'levels', 'skillLevels', 'series'));
    }
}
