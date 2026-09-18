<?php

namespace App\Http\Controllers;

use App\Models\PersonalizedGuidanceRequest;
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
