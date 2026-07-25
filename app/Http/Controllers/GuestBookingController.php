<?php

namespace App\Http\Controllers;

use App\Models\GuestBooking;
use App\Models\BookingAvailability;
use App\Services\GoogleCalendarService;
use App\Services\ZoomService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Mail;
use App\Mail\GuestBookingConfirmed;
use App\Mail\NewGuestBookingNotification;

class GuestBookingController extends Controller
{
    /**
     * Bookings must be made at least 24 hours in advance so Kingsley has time to prepare.
     */
    private function earliestBookableAt()
    {
        return now()->addHours(24);
    }

    public function getAllAvailableSlots()
    {
        $cutoff = $this->earliestBookableAt();

        // Booked slots are included (not filtered out) so the calendar can
        // show them as disabled rather than hiding them entirely.
        $slots = BookingAvailability::where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get()
            ->filter(fn ($slot) => \Carbon\Carbon::parse("{$slot->date} {$slot->time}") >= $cutoff)
            ->values();

        return response()->json($slots);
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $cutoff = $this->earliestBookableAt();

        $slots = BookingAvailability::where('date', $request->date)
            ->get(['time', 'is_booked'])
            ->filter(fn ($slot) => \Carbon\Carbon::parse("{$request->date} {$slot->time}") >= $cutoff)
            ->values();

        return response()->json($slots);
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'focus' => 'nullable|string',
            'skillLevel' => 'nullable|string',
            'paymentMethod' => 'required|in:stripe,paystack',
            'timezone' => 'nullable|string|max:64',
        ]);

        // Check availability
        $availability = BookingAvailability::where('date', $request->date)
            ->where('time', $request->time)
            ->first();

        if (!$availability || $availability->is_booked) {
            return response()->json(['error' => 'This time slot is no longer available.'], 422);
        }

        if (\Carbon\Carbon::parse("{$request->date} {$request->time}") < $this->earliestBookableAt()) {
            return response()->json(['error' => 'Bookings must be made at least 24 hours in advance.'], 422);
        }

        $reference = Str::uuid()->toString();

        $booking = GuestBooking::create([
            'name' => $request->name,
            'email' => $request->email,
            'timezone' => $request->timezone,
            'date' => $request->date,
            'time' => $request->time,
            'focus' => $request->focus,
            'skill_level' => $request->skillLevel ?? '',
            'payment_method' => $request->paymentMethod,
            'payment_status' => 'pending',
        ]);

        if (config('services.fake_guest_payments')) {
            $fakeRef = Str::uuid()->toString();

            if ($request->paymentMethod === 'stripe') {
                $booking->update(['stripe_session_id' => $fakeRef]);
                $url = route('guest-booking.success', ['provider' => 'stripe']) . '?session_id=' . $fakeRef;
            } else {
                $booking->update(['paystack_reference' => $fakeRef]);
                $url = route('guest-booking.success', ['provider' => 'paystack']) . '?reference=' . $fakeRef;
            }

            return response()->json(['url' => $url]);
        }

        if ($request->paymentMethod === 'stripe') {
            return $this->initiateStripe($booking);
        } else {
            return $this->initiatePaystack($booking, $reference);
        }
    }

    private function initiateStripe(GuestBooking $booking)
    {
        Stripe::setApiKey(config('stripe.secret_key'));

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'One-on-One Piano Lesson with Kingsley Khord',
                        ],
                        'unit_amount' => 60 * 100, // $60.00
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('guest-booking.success', ['provider' => 'stripe']) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('guest-booking.cancel'),
                'customer_email' => $booking->email,
                'metadata' => [
                    'booking_id' => $booking->id,
                ],
            ]);

            $booking->update(['stripe_session_id' => $session->id]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            Log::error('Stripe initiation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Stripe initiation failed'], 500);
        }
    }

    private function initiatePaystack(GuestBooking $booking, $reference)
    {
        try {
            $payload = [
                'email' => $booking->email,
                'amount' => 80000 * 100, // #80000.00 in cents
              
                'reference' => $reference,
                'callback_url' => route('guest-booking.success', ['provider' => 'paystack']),
                'metadata' => [
                    'booking_id' => $booking->id,
                ],
            ];

            $response = Http::withToken(config('services.paystack.secret_key'))
                ->post('https://api.paystack.co/transaction/initialize', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $booking->update(['paystack_reference' => $reference]);
                return response()->json(['url' => $data['data']['authorization_url']]);
            }

            Log::error('Paystack initiation failed: ' . $response->body());
            return response()->json(['error' => 'Paystack initiation failed'], 500);
        } catch (\Exception $e) {
            Log::error('Paystack initiation exception: ' . $e->getMessage());
            return response()->json(['error' => 'Paystack initiation failed'], 500);
        }
    }

    public function handleSuccess(Request $request, $provider)
    {
        $booking = null;

        if ($provider === 'stripe') {
            $sessionId = $request->query('session_id');
            $booking = GuestBooking::where('stripe_session_id', $sessionId)->first();
            
            // In a real app, we'd verify with Stripe API here
            if ($booking && $booking->payment_status === 'pending') {
                $this->finalizeBooking($booking);
            }
        } elseif ($provider === 'paystack') {
            $reference = $request->query('reference');
            $booking = GuestBooking::where('paystack_reference', $reference)->first();

            if ($booking && $booking->payment_status === 'pending') {
                $this->finalizeBooking($booking);
            }
        }

        if ($booking) {
            return view('guest_booking_success', compact('booking'));
        }

        return redirect('/')->with('error', 'Booking not found or already processed.');
    }

    public function handleCancel()
    {
        return redirect('/')->with('error', 'Booking payment was cancelled.');
    }

    private function finalizeBooking(GuestBooking $booking)
    {
        $booking->update(['payment_status' => 'paid']);

        // Mark slot as booked
        BookingAvailability::where('date', $booking->date)
            ->where('time', $booking->time)
            ->update(['is_booked' => true]);

        // Slots are entered/stored in West Africa Time (the studio's business timezone).
        $startWat = Carbon::parse($booking->date . ' ' . $booking->time, 'Africa/Lagos');
        $endWat = $startWat->copy()->addHour();

        try {
            $meeting = (new ZoomService())->createMeeting([
                'topic' => 'One-on-One Session with ' . $booking->name,
                'start_time' => $startWat->clone()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z'),
                'duration' => 60,
                'timezone' => 'Africa/Lagos',
            ]);

            $booking->update([
                'zoom_join_url' => $meeting['join_url'] ?? null,
                'zoom_meeting_id' => $meeting['id'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to create Zoom meeting for guest booking: ' . $e->getMessage());
        }

        try {
            $event = (new GoogleCalendarService())->createEvent([
                'title' => 'One-on-One Session with ' . $booking->name,
                'description' => "Booked by {$booking->name} ({$booking->email}) via the membership site."
                    . ($booking->skill_level ? "\nSkill level: {$booking->skill_level}" : '')
                    . ($booking->focus ? "\nFocus: {$booking->focus}" : '')
                    . ($booking->zoom_join_url ? "\n\nZoom link: {$booking->zoom_join_url}" : ''),
                'start_time' => $startWat->toRfc3339String(),
                'end_time' => $endWat->toRfc3339String(),
                'timezone' => 'Africa/Lagos',
            ]);

            if ($event) {
                $booking->update(['google_calendar_event_id' => $event['event_id'] ?? null]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to create Google Calendar event for guest booking: ' . $e->getMessage());
        }

        try {
            // Send email notification
            Mail::to($booking->email)->send(new GuestBookingConfirmed($booking));

            // Notify the admin
            Mail::to(config('services.admin_notification_email'))->send(new NewGuestBookingNotification($booking));
        } catch (\Exception $e) {
            Log::warning('Failed to send guest booking emails: ' . $e->getMessage());
        }
    }
}
