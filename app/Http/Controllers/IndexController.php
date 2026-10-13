<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Liveshow;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function index()
    {
        $extracourses = \App\Models\ExtraCourse::where('status', 'active')->latest()->take(3)->get();
        $liveshow = Liveshow::where('start_time', '>=', Carbon::now())
        ->orderBy('start_time', 'asc')
        ->first();

        $latestLessons = [];

        $latestPianoExercise = Upload::where('category', 'piano exercise')
            ->orderBy('created_at', 'desc')
            ->first();
        // Etudes & Pieces share the Piano Exercise slot: show whichever is newest.
        $latestEtude = \App\Models\Etude::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();
        if ($latestEtude && (! $latestPianoExercise || $latestEtude->created_at > $latestPianoExercise->created_at)) {
            $latestPianoExercise = $latestEtude;
        }
        if ($latestPianoExercise) {
            $latestLessons['piano exercise'] = $latestPianoExercise;
        }

        $latestGuidedPractice = \App\Models\MusicalApplication::orderBy('created_at', 'desc')->first();
        if ($latestGuidedPractice) {
            $latestLessons['guided practice'] = $latestGuidedPractice;
        }

        $latestLearnSong = \App\Models\LearnSong::orderBy('created_at', 'desc')->first();
        if ($latestLearnSong) {
            $latestLessons['learn songs'] = $latestLearnSong;
        }

        $latestExtraCourse = \App\Models\ExtraCourse::orderBy('created_at', 'desc')->first();
        if ($latestExtraCourse) {
            $latestLessons['extra courses'] = $latestExtraCourse;
        }

        return view('welcome', compact('extracourses', 'liveshow', 'latestLessons'));
    }
}
