<?php

namespace App\Http\Controllers;

use App\Models\LiveCoachingBooking;
use App\Models\Liveshow;
use App\Models\PersonalizedGuidanceRequest;
use App\Models\PersonalizedPlan;
use App\Models\UserDailyLogin;
use Illuminate\Http\Request;

class PersonalizedGuidanceController extends Controller
{
    public function create()
    {
        $existingRequest = PersonalizedGuidanceRequest::where('user_id', auth()->id())
            ->latest()
            ->first();

        return view('memberpages.personalized-guidance', compact('existingRequest'));
    }

    public function plan()
    {
        if (! auth()->user()->premium) {
            return view('memberpages.personalized-plan-paywall');
        }

        $plan = PersonalizedPlan::where('user_id', auth()->id())->first();

        if (! $plan) {
            $hasSubmittedRequest = PersonalizedGuidanceRequest::where('user_id', auth()->id())->exists();
            $hasBookedCall = LiveCoachingBooking::where('user_id', auth()->id())->exists();

            if ($hasSubmittedRequest || $hasBookedCall) {
                return view('memberpages.personalized-plan-pending');
            }

            return view('memberpages.personalized-plan-empty');
        }

        $userId = auth()->id();
        $timezone = auth()->user()->timezone;

        $liveshow = Liveshow::where('category', 'session')
            ->where('start_time', '>=', now())
            ->where(function ($query) {
                $query->where('access_type', 'all');
                if (auth()->user()->premium) {
                    $query->orWhere('access_type', 'premium');
                }
            })
            ->orderBy('start_time')
            ->first();

        $dayStatuses = UserDailyLogin::thisWeekStatuses($userId, $timezone);
        $streak = UserDailyLogin::currentStreak($userId, $timezone);

        // Same milestone ladder and "current level" source used on the main
        // dashboard's stats widget (components/memberarea/stats.blade.php),
        // kept in sync here so both pages report the same numbers.
        $milestonesList = [
            ['name' => 'Starter', 'lessons' => 1],
            ['name' => 'Player', 'lessons' => 4],
            ['name' => 'Performer', 'lessons' => 9],
            ['name' => 'Artist', 'lessons' => 14],
            ['name' => 'Maestro', 'lessons' => 21],
            ['name' => 'Master', 'lessons' => 31],
            ['name' => 'Grand Master', 'lessons' => 43],
            ['name' => 'Composer', 'lessons' => 58],
            ['name' => 'Conductor', 'lessons' => 76],
            ['name' => 'Virtuoso', 'lessons' => 96],
            ['name' => 'Prodigy', 'lessons' => 121],
            ['name' => 'Piano Legend', 'lessons' => 151],
        ];

        $courseProgressCount = \Illuminate\Support\Facades\DB::table('course_progress')
            ->where('user_id', $userId)
            ->distinct('course_id')
            ->count('course_id');
        $lessonCompletionCount = \App\Models\LessonCompletion::where('user_id', $userId)->count();
        $totalCompleted = $courseProgressCount + $lessonCompletionCount;

        $achievedCount = 0;
        $currentMilestoneName = 'None';
        foreach ($milestonesList as $milestone) {
            if ($totalCompleted >= $milestone['lessons']) {
                $achievedCount++;
                $currentMilestoneName = $milestone['name'];
            } else {
                break;
            }
        }

        $assessment = \App\Models\UserAssessment::where('user_id', $userId)->latest()->first();
        $currentLevel = $assessment ? $assessment->skill_level : 'Nil';

        $stats = [
            'lessons_completed' => count(array_intersect($plan->completed_lessons ?? [], $plan->allLessonKeys())),
            'lessons_total' => count($plan->allLessonKeys()),
            'current_streak' => $streak,
            'milestone_name' => $currentMilestoneName,
            'milestone_count' => $achievedCount,
            'current_level' => $currentLevel,
        ];

        return view('memberpages.personalized-plan', compact('plan', 'liveshow', 'dayStatuses', 'streak', 'stats'));
    }

    public function toggleLesson(Request $request)
    {
        $validated = $request->validate([
            'lesson_key' => ['required', 'string'],
            'completed' => ['required', 'boolean'],
        ]);

        $plan = PersonalizedPlan::where('user_id', auth()->id())->firstOrFail();

        if (! in_array($validated['lesson_key'], $plan->allLessonKeys(), true)) {
            abort(404);
        }

        $completed = collect($plan->completed_lessons ?? []);

        $completed = $validated['completed']
            ? $completed->push($validated['lesson_key'])->unique()
            : $completed->reject(fn ($key) => $key === $validated['lesson_key']);

        $plan->update(['completed_lessons' => $completed->values()->all()]);

        return response()->json(['progress' => $plan->progressPercent()]);
    }

    public function store(Request $request)
    {
        if (! auth()->user()->premium) {
            return response()->json(['message' => 'This feature is for Premium members only.'], 403);
        }

        $validated = $request->validate([
            'youtube_link' => ['required', 'string', 'max:500', 'regex:/(youtube\.com|youtu\.be|drive\.google\.com)/i'],
            'chord_vocabulary' => ['required', 'string', 'max:100'],
            'key_fluency' => ['required', 'string', 'max:100'],
            'inspiration_pianist' => ['required', 'string', 'max:150'],
            'archetype' => ['required', 'string', 'max:100'],
            'practice_days_per_week' => ['required', 'string', 'max:50'],
            'practice_time_per_day' => ['required', 'string', 'max:50'],
            'playing_by_ear' => ['required', 'string', 'max:150'],
            'experience_level' => ['required', 'string', 'max:100'],
            'style_focus' => ['required', 'string', 'max:100'],
            'primary_goal' => ['required', 'string', 'max:3000'],
            'details' => ['nullable', 'string', 'max:3000'],
        ]);

        $guidanceRequest = PersonalizedGuidanceRequest::create([
            'user_id' => auth()->id(),
            'youtube_link' => $validated['youtube_link'],
            'chord_vocabulary' => $validated['chord_vocabulary'] ?? null,
            'key_fluency' => $validated['key_fluency'] ?? null,
            'inspiration_pianist' => $validated['inspiration_pianist'] ?? null,
            'archetype' => $validated['archetype'] ?? null,
            'practice_days_per_week' => $validated['practice_days_per_week'] ?? null,
            'practice_time_per_day' => $validated['practice_time_per_day'] ?? null,
            'playing_by_ear' => $validated['playing_by_ear'] ?? null,
            'experience_level' => $validated['experience_level'] ?? null,
            'style_focus' => $validated['style_focus'] ?? null,
            'primary_goal' => $validated['primary_goal'] ?? null,
            'details' => $validated['details'] ?? null,
        ]);

        return response()->json([
            'message' => 'Thanks! Your video and notes have been sent for review.',
            'request' => $guidanceRequest,
        ]);
    }
}
