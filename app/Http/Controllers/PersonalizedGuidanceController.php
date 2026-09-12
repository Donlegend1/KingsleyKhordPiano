<?php

namespace App\Http\Controllers;

use App\Models\PersonalizedGuidanceRequest;
use Illuminate\Http\Request;

class PersonalizedGuidanceController extends Controller
{
    public function store(Request $request)
    {
        if (! auth()->user()->premium) {
            return response()->json(['message' => 'This feature is for Premium members only.'], 403);
        }

        $validated = $request->validate([
            'youtube_link' => ['required', 'url', 'regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\/.+/i'],
            'details' => ['nullable', 'string', 'max:3000'],
        ]);

        $guidanceRequest = PersonalizedGuidanceRequest::create([
            'user_id' => auth()->id(),
            'youtube_link' => $validated['youtube_link'],
            'details' => $validated['details'] ?? null,
        ]);

        return response()->json([
            'message' => 'Thanks! Your video and notes have been sent for review.',
            'request' => $guidanceRequest,
        ]);
    }
}
