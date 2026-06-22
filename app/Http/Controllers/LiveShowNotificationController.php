<?php

namespace App\Http\Controllers;

use App\Models\LiveShowNotification;
use App\Http\Requests\StoreLiveShowNotificationRequest;
use App\Http\Requests\UpdateLiveShowNotificationRequest;

class LiveShowNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $user = auth()->user();

        // Check if the user is already subscribed
        $existingSubscription = LiveShowNotification::where('user_id', $user->id)->first();
        if ($existingSubscription) {
            return response()->json(['message' => 'You are already subscribed to live show notifications.'], 400);
        }

        // Create a new subscription
        LiveShowNotification::create([
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Subscribed to live show notifications successfully.']);
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
