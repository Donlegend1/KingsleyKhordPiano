<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestBooking;
use App\Models\BookingAvailability;
use Illuminate\Http\Request;
use App\Mail\GuestBookingConfirmed;
use Illuminate\Support\Facades\Mail;

class GuestBookingController extends Controller
{
    public function index()
    {
        $bookings = GuestBooking::orderBy('date', 'desc')->orderBy('time', 'desc')->paginate(15);
        return view('admin.guest_bookings.index', compact('bookings'));
    }

    public function updateMeetingLink(Request $request, GuestBooking $booking)
    {
        $request->validate([
            'google_meet_link' => 'required',
        ]);

        $booking->update([
            'google_meet_link' => $request->google_meet_link,
        ]);

        // Optionally resend email if link is updated
        Mail::to($booking->email)->send(new GuestBookingConfirmed($booking));

        return back()->with('success', 'Meeting link updated and email sent to guest.');
    }

    public function availability()
    {
        $availabilities = BookingAvailability::orderBy('date', 'asc')->orderBy('time', 'asc')->get()->groupBy('date');
        return view('admin.guest_bookings.availability', compact('availabilities'));
    }

    public function storeAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        BookingAvailability::updateOrCreate(
            ['date' => $request->date, 'time' => $request->time],
            ['is_booked' => false]
        );

        return back()->with('success', 'Availability slot added.');
    }

    public function destroyAvailability(BookingAvailability $availability)
    {
        if ($availability->is_booked) {
            return back()->with('error', 'Cannot delete a slot that is already booked.');
        }

        $availability->delete();
        return back()->with('success', 'Availability slot removed.');
    }
}
