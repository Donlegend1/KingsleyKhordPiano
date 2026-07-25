<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\CourseVideoComment;
use App\Models\User;
use App\Services\MidiPracticeFileResolver;

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

    public function pianoExercisePlayer(Request $request)
    {
        $level = $request->query('level');
        $series = $request->query('series');
        $skillLevel = $request->query('skill_level', 'Basic');
        
        if ($series) {
            $playlistQuery = \App\Models\MusicalApplication::where('series', $series);
        } elseif ($level) {
            $playlistQuery = Upload::where('category', 'piano exercise')
                         ->where('level', $level)
                         ->where('skill_level', $skillLevel);
        } else {
            return redirect()->route('piano.exercise');
        }
        
        $playlist = $playlistQuery->orderBy('id', 'asc')->get();
        
        if ($playlist->isEmpty()) {
            return redirect()->route('piano.exercise')->with('error', 'No exercises found.');
        }

        $activeVideoId = $request->query('video_id');
        $activeVideo = $activeVideoId ? $playlist->firstWhere('id', $activeVideoId) : $playlist->first();
        
        $service = app(\App\Services\BookmarkService::class);
        $isBookmarked = $activeVideo ? $service->isBookmarked($activeVideo) : false;

        $comments = CourseVideoComment::where('course_id', $activeVideo->id)
                            ->where('category', 'piano exercise')
                            ->get();

        $levels = ['independence', 'technique', 'flexibility', 'strength', 'dexterity'];
        $skillLevels = ['Basic', 'Competent', 'Challenging'];

        $related_courses = [];
        if ($activeVideo && !empty($activeVideo->tags)) {
            $related_courses = Upload::whereIn('id', $activeVideo->tags)->get();
        }

        $midiPracticeFile = app(MidiPracticeFileResolver::class)->forLesson($activeVideo);
        $midiPracticeFiles = \App\Models\MidiFile::whereNotNull('midi_file_path')->orderBy('name')->get();

        return view('memberpages.series-player', compact(
            'playlist', 
            'activeVideo', 
            'level', 
            'series', 
            'skillLevel', 
            'isBookmarked', 
            'levels', 
            'skillLevels', 
            'comments',
            'related_courses',
            'midiPracticeFile',
            'midiPracticeFiles'
        ));
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
            'course_id' => 'required',
            'category' => 'required|string',
        ]);

        CourseVideoComment::create([
            'user_id' => \Auth::id(),
            'course_id' => $request->course_id,
            'category' => $request->category,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Comment posted successfully.');
    }

    public function fingerExercises(Request $request)
    {
        $skillLevel = $request->query('skill_level', 'Basic');
        $skillLevels = ['Basic', 'Competent', 'Challenging'];
        
        return view('memberpages.finger-exercises', compact('skillLevel', 'skillLevels'));
    }

    public function musicalApplication(Request $request)
    {
        $skillLevel = $request->query('skill_level', 'ALL');
        $skillLevels = ['ALL', 'Beginner', 'Intermediate', 'Advanced'];
        
        $query = \App\Models\MusicalApplication::where('status', 'active');
        if ($skillLevel !== 'ALL') {
            $query->where('skill_level', $skillLevel);
        }
        
        $applications = $query->get()->groupBy('series');

        return view('memberpages.musical-application', compact('skillLevel', 'skillLevels', 'applications'));
    }
}
