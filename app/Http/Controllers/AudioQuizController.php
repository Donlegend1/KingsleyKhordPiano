<?php

namespace App\Http\Controllers;

use App\Models\LessonCompletion;
use App\Models\Quiz;

class AudioQuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $relativePitch = Quiz::where('category', 'Relative Pitch')->orderBy('id')->first();
        $melodicDictation = Quiz::where('category', 'Melodic Dictation')->orderBy('id')->first();
        $intervals = Quiz::where('category', 'Diatonic Intervals')->orderBy('id')->first();
        $basicTriads = Quiz::where('category', 'Basic Triad')->orderBy('id')->first();
        $add9 = Quiz::where('category', 'Add 9 & b9')->orderBy('id')->first();
        $seventhDegree = Quiz::where('category', '7th Degree Chords')->orderBy('id')->first();
        $secondarySeventh = Quiz::where('category', 'Secondary 7th Chords')->orderBy('id')->first();
        $ninthDegree = Quiz::where('category', '9th Degree Chords')->orderBy('id')->first();
        $secondaryNinth = Quiz::where('category', 'Secondary 9th Chords')->orderBy('id')->first();
        $eleventhDegree = Quiz::where('category', '11th Degree Chords')->orderBy('id')->first();
        $secondaryEleventh = Quiz::where('category', 'Secondary 11th Chords')->orderBy('id')->first();
        $thirteenthDegree = Quiz::where('category', '13th Degree Chords')->orderBy('id')->first();
        $extensionsRecognition = Quiz::where('category', 'Extentions recognition')->orderBy('id')->first();
        $chordProgressions = Quiz::where('category', 'Chord Progressions')->orderBy('id')->first();
        $modalVoicings = Quiz::where('category', 'Modal Voicings')->orderBy('id')->first();
        $scales = Quiz::where('category', 'Scales')->orderBy('id')->first();

        // Percentage of lessons completed per category, for the progress bar
        // on each card — a category can have more than one lesson (Quiz row)
        // even though the card only links to the first one.
        $categories = [
            'Relative Pitch', 'Melodic Dictation', 'Diatonic Intervals', 'Basic Triad',
            'Add 9 & b9', '7th Degree Chords', 'Secondary 7th Chords', '9th Degree Chords',
            'Secondary 9th Chords', '11th Degree Chords', 'Secondary 11th Chords',
            '13th Degree Chords', 'Extentions recognition', 'Chord Progressions',
            'Modal Voicings', 'Scales',
        ];

        $quizIdsByCategory = Quiz::whereIn('category', $categories)
            ->get(['id', 'category'])
            ->groupBy('category')
            ->map(fn ($quizzes) => $quizzes->pluck('id'));

        $completedIds = LessonCompletion::where('user_id', auth()->id())
            ->where('completable_type', Quiz::class)
            ->whereIn('completable_id', $quizIdsByCategory->flatten())
            ->pluck('completable_id')
            ->all();

        $categoryProgress = collect($categories)->mapWithKeys(function ($category) use ($quizIdsByCategory, $completedIds) {
            $ids = $quizIdsByCategory->get($category, collect());
            $total = $ids->count();
            $completed = $ids->filter(fn ($id) => in_array($id, $completedIds, true))->count();

            return [$category => [
                'total' => $total,
                'completed' => $completed,
                'pct' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            ]];
        });

        return view('memberpages.audio-quiz', compact('relativePitch', 'melodicDictation', 'intervals', 'basicTriads', 'add9', 'seventhDegree', 'secondarySeventh', 'ninthDegree', 'secondaryNinth', 'eleventhDegree', 'secondaryEleventh', 'thirteenthDegree', 'extensionsRecognition', 'chordProgressions', 'modalVoicings', 'scales', 'categoryProgress'));
    }
}
