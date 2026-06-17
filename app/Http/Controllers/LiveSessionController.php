<?php

namespace App\Http\Controllers;

use App\Models\Liveshow;

class LiveSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the live session page.
     *
     * @return \Illuminate\View\View
     */
    function liveSession() 
    {
        return view('memberpages.livesession');
    }

    public function confirmBooking(Liveshow $liveshow)
    {
        if ($liveshow->category !== 'session') {
            return redirect('/member/live-session')->with('error', 'This liveshow is not a live session.');
        }

        $userId = auth()->id();
        $isBooked = $liveshow->bookedUsers()->where('users.id', $userId)->exists();
        $bookingsCount = $liveshow->bookedUsers()->count();
        $isFull = $bookingsCount >= $liveshow->max_slots;

        // If user already booked, redirect to live-session page with info
        if ($isBooked) {
            return redirect('/member/live-session')->with('info', 'You have already booked a slot for this session.');
        }

        // If the session is full, redirect
        if ($isFull) {
            return redirect('/member/live-session')->with('error', 'This live session is already full.');
        }

        // Get already booked participants' avatars/names
        $bookedUsers = $liveshow->bookedUsers;

        return view('memberpages.confirm-slot', compact('liveshow', 'bookedUsers', 'bookingsCount'));
    }

    public function bookSlot(Liveshow $liveshow)
    {
        if ($liveshow->category !== 'session') {
            return redirect('/member/live-session')->with('error', 'This liveshow is not a live session.');
        }

        $userId = auth()->id();
        $bookingsCount = $liveshow->bookedUsers()->count();

        if ($liveshow->bookedUsers()->where('users.id', $userId)->exists()) {
            return redirect('/member/live-session')->with('info', 'You have already booked a slot for this session.');
        }

        if ($bookingsCount >= $liveshow->max_slots) {
            return redirect('/member/live-session')->with('error', 'This live session is already full.');
        }

        // Book slot
        $liveshow->bookedUsers()->attach($userId);

        return redirect('/member/live-session')->with('success', 'Your live session slot has been booked successfully!');
    }
}
