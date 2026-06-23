<?php

namespace App\Http\Controllers;

use App\Models\LiveCoachingBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveCoachingBookingController extends Controller
{
    /**
     * Carbon::dayOfWeek => list of [start hour, start minute, end hour, end minute],
     * in West Africa Time (UTC+1, no DST). Wednesday and Sunday are closed.
     */
    private const SCHEDULE = [
        Carbon::MONDAY => [[9, 0, 9, 45], [15, 30, 16, 15], [19, 20, 20, 5]],
        Carbon::TUESDAY => [[14, 0, 14, 45]],
        Carbon::WEDNESDAY => [[11, 30, 12, 15], [17, 0, 17, 45]],
        Carbon::FRIDAY => [[18, 10, 19, 5]],
        Carbon::SATURDAY => [[11, 30, 12, 15], [17, 0, 17, 45]],
    ];

    private const WINDOW_DAYS = 14;

    public function index()
    {
        $user = Auth::user();

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $sessionsIncluded = 1;
        $sessionsUsed = LiveCoachingBooking::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->count();

        $nextResetLabel = now()->startOfMonth()->addMonthNoOverflow()->format('F j, Y');

        $windowStart = now()->toDateString();
        $windowEnd = now()->addDays(self::WINDOW_DAYS - 1)->toDateString();

        // Every booked slot in the visible window, regardless of who booked it,
        // so they can be shown as disabled for everyone.
        $bookedSlots = LiveCoachingBooking::whereBetween('date', [$windowStart, $windowEnd])
            ->get(['date', 'time'])
            ->groupBy(fn ($b) => Carbon::parse($b->date)->toDateString())
            ->map(fn ($rows) => $rows->map(fn ($b) => substr($b->time, 0, 8))->values())
            ->toArray();

        $activeBooking = LiveCoachingBooking::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->first();

        return view('memberpages.my-library', [
            'sessionsUsed' => $sessionsUsed,
            'sessionsIncluded' => $sessionsIncluded,
            'nextResetLabel' => $nextResetLabel,
            'bookedSlots' => $bookedSlots,
            'activeBooking' => $activeBooking,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i:s',
        ]);

        $user = Auth::user();
        $date = Carbon::parse($request->date);
        $time = $request->time;

        // 1) Must be a valid recurring slot for that weekday.
        $validTimes = array_map(
            fn ($s) => sprintf('%02d:%02d:00', $s[0], $s[1]),
            self::SCHEDULE[$date->dayOfWeek] ?? []
        );
        if (!in_array($time, $validTimes, true)) {
            return response()->json(['error' => 'That is not a valid time slot.'], 422);
        }

        // 2) Must be within the booking window.
        $windowStart = now()->startOfDay();
        $windowEnd = now()->addDays(self::WINDOW_DAYS - 1)->endOfDay();
        $slotDateTime = Carbon::parse($request->date . ' ' . $time);
        if ($slotDateTime->lt($windowStart) || $slotDateTime->gt($windowEnd)) {
            return response()->json(['error' => 'This date is outside the booking window.'], 422);
        }

        // 3) Must be at least 24 hours out.
        if ($slotDateTime->lt(now()->addHours(24))) {
            return response()->json(['error' => 'Bookings must be made at least 24 hours in advance.'], 422);
        }

        // 4) User must still have their free session available this month.
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $sessionsUsed = LiveCoachingBooking::where('user_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->count();
        if ($sessionsUsed >= 1) {
            return response()->json(['error' => 'You have already used your free session this month.'], 422);
        }

        // 5) Slot must not already be booked by anyone else.
        $alreadyBooked = LiveCoachingBooking::where('date', $request->date)
            ->where('time', $time)
            ->exists();
        if ($alreadyBooked) {
            return response()->json(['error' => 'This time slot is no longer available.'], 422);
        }

        $booking = LiveCoachingBooking::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'time' => $time,
        ]);

        $booking->load('user');

        try {
            // Send notification to the member who booked
            $user->notify(new \App\Notifications\CoachingBookingNotification($booking));

            // Send notification to the admin
            \Illuminate\Support\Facades\Notification::route('mail', 'Kingsleykhord@gmail.com')
                ->notify(new \App\Notifications\AdminCoachingBookingNotification($booking));
        } catch (\Exception $e) {
            logger()->warning('Failed to send live coaching session booking emails: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'booking' => $booking]);
    }
}
