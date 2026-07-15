<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\CourseVideoComment;
use App\Models\User;

class ExerciseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function pianoExercise(Request $request)
    {
        try {
            \App\Models\CategoryView::markViewed(auth()->id(), 'piano_exercise');

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
                $query->where('series', $series)->orderByRaw('position IS NULL, position ASC')->orderBy('id', 'asc');
            } else {
                $subquery = Upload::where('category', 'piano exercise')
                    ->when($level, fn ($q) => $q->where('level', $level))
                    ->when($skillLevel, fn ($q) => $q->where('skill_level', $skillLevel))
                    ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
                    ->selectRaw('MIN(id) as id')
                    ->groupBy(\DB::raw('COALESCE(series, CAST(id AS CHAR))'));

                $query->whereIn('id', $subquery)
                    ->select('uploads.*')
                    ->selectSub(function ($q) {
                        $q->from('uploads as u2')
                            ->whereRaw('COALESCE(u2.series, CAST(u2.id AS CHAR)) = COALESCE(uploads.series, CAST(uploads.id AS CHAR))')
                            ->selectRaw('count(*)');
                    }, 'item_count')
                    ->orderByRaw('position IS NULL, position ASC')
                    ->latest();
            }

            $exercises = $query->paginate(12);

            $levels = ['independence', 'technique', 'flexibility', 'strength', 'dexterity'];
            $skillLevels = ['Basic', 'Competent', 'Challenging'];

            return view('memberpages.pianoexercise', compact(
                'exercises',
                'level',
                'skillLevel',
                'search',
                'levels',
                'skillLevels',
                'series'
            ));
        } catch (\Throwable $e) {
            logger()->error('Piano Exercise Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('dashboard')
                ->with('error', 'An error occurred while loading piano exercises.');
        }
    }

        public function pianoExercisePlayer(Request $request)
    {
        $level = $request->query('level');
        $series = $request->query('series');
        $skillLevel = $request->query('skill_level');

        $skillLevels = ['Basic', 'Competent', 'Challenging'];
        $groupedPlaylist = collect();

        if ($series) {
            $playlist = \App\Models\MusicalApplication::where('series', $series)
                ->orderBy('id', 'asc')
                ->get();
        } elseif ($level) {
            // Finger exercises browse all skill levels for this focus area at
            // once (grouped in the sidebar) instead of being locked to one
            // skill level chosen on the previous page. skill_level is stored
            // inconsistently cased in the DB (e.g. "basic" vs "Basic"), so
            // every comparison here is done case-insensitively.
            $skillOrder = array_flip(array_map('strtolower', $skillLevels));

            $playlist = Upload::where('category', 'piano exercise')
                ->where('level', $level)
                ->orderByRaw('position IS NULL, position ASC')
                ->orderBy('id', 'asc')
                ->get()
                ->sortBy(fn($item) => ($skillOrder[strtolower($item->skill_level ?? '')] ?? count($skillLevels)) * 1000000 + $item->id)
                ->values();

            $groupedPlaylist = collect($skillLevels)
                ->mapWithKeys(fn($lvl) => [
                    $lvl => $playlist->filter(fn($item) => strtolower($item->skill_level ?? '') === strtolower($lvl))->values(),
                ])
                ->filter(fn($items) => $items->isNotEmpty());
        } else {
            return redirect()->route('piano.exercise');
        }

        if ($playlist->isEmpty()) {
            return redirect()->route('piano.exercise')->with('error', 'No exercises found.');
        }

            $activeVideoId = $request->query('video_id');
            $activeVideo = $activeVideoId
                ? $playlist->firstWhere('id', $activeVideoId)
                : $playlist->first();

        if ($activeVideo) {
            \App\Models\LessonView::record(auth()->id(), $activeVideo);
            $skillLevel = $level ? $activeVideo->skill_level : $skillLevel;
        }

        $skillLevel = $skillLevel ?? 'Basic';

            $service = app(\App\Services\BookmarkService::class);
            $isBookmarked = $activeVideo
                ? $service->isBookmarked($activeVideo)
                : false;

        $comments = CourseVideoComment::where('course_id', $activeVideo->id)
                            ->where('category', 'piano exercise')
                            ->with(['user', 'replies.user'])
                            ->get();

        $levels = ['independence', 'technique', 'flexibility', 'strength', 'dexterity'];

            $related_courses = [];

            if ($activeVideo && !empty($activeVideo->tags)) {
                if ($activeVideo instanceof \App\Models\MusicalApplication) {
                    $related_courses = \App\Models\MusicalApplication::whereIn('id', $activeVideo->tags)->get();
                } else {
                    $related_courses = Upload::whereIn('id', $activeVideo->tags)->get();
                }
            }

        return view('memberpages.series-player', compact(
            'playlist',
            'groupedPlaylist',
            'activeVideo',
            'level',
            'series',
            'skillLevel',
            'isBookmarked',
            'levels',
            'skillLevels',
            'comments',
            'related_courses'
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

    public function fingerExercises()
    {
        return view('memberpages.finger-exercises');
    }

    public function musicalApplication(Request $request)
    {
        $skillLevel = $request->query('skill_level', 'ALL');
        $skillLevels = ['ALL', 'Beginner', 'Intermediate', 'Advanced'];
        $page = $request->query('page', 1);

        $baseQuery = fn() => \App\Models\MusicalApplication::where('status', 'active')
            ->when($skillLevel !== 'ALL', fn($q) => $q->where('skill_level', $skillLevel))
            ->orderByRaw('position IS NULL, position ASC');

        $seriesPage = $baseQuery()
            ->select('series')
            ->selectRaw('MAX(created_at) as latest_created_at')
            ->groupBy('series')
            ->orderByDesc('latest_created_at')
            ->paginate(9, ['*'], 'page', $page)
            ->appends(['skill_level' => $skillLevel]);

        $seriesNames = collect($seriesPage->items())->pluck('series');

        $applications = $baseQuery()
            ->where(function($q) use ($seriesNames) {
                $named = $seriesNames->filter()->values();
                if ($named->isNotEmpty()) {
                    $q->whereIn('series', $named);
                }
                if ($seriesNames->contains(null)) {
                    $q->orWhereNull('series');
                }
            })
            ->get()
            ->groupBy('series')
            ->sortBy(fn($items, $series) => $seriesNames->search($series));

        return view('memberpages.musical-application', compact('skillLevel', 'skillLevels', 'applications', 'seriesPage'));
    }
}
