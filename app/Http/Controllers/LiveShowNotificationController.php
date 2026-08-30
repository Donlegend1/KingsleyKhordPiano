<?php

namespace App\Http\Controllers;

use App\Models\LiveShowNotification;
use App\Http\Requests\StoreLiveShowNotificationRequest;
use App\Http\Requests\UpdateLiveShowNotificationRequest;

class LiveShowNotificationController extends Controller
{
    /**
     * Whether the current user is subscribed to live show emails.
     */
    public function index()
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['subscribed' => false], 401);
        }

        return response()->json([
            'subscribed' => LiveShowNotification::where('user_id', $user->id)->exists(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLiveShowNotificationRequest $request)
    {
        $user = $request->user() ?? auth()->user();

        if (! $user) {
            return response()->json(['message' => 'You must be logged in to subscribe.'], 401);
        }

        LiveShowNotification::firstOrCreate([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Subscribed to live show notifications successfully.',
            'subscribed' => true,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(LiveShowNotification $liveShowNotification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LiveShowNotification $liveShowNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLiveShowNotificationRequest $request, LiveShowNotification $liveShowNotification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LiveShowNotification $liveShowNotification)
    {
        //
    }
}
