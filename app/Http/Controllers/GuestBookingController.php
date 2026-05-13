<?php

namespace App\Http\Controllers;

use App\Models\GuestBooking;
use App\Models\BookingAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Mail;
use App\Mail\GuestBookingConfirmed;

class GuestBookingController extends Controller
{
    public function getAllAvailableSlots()
    {
        $slots = BookingAvailability::where('date', '>=', now()->toDateString())
            ->where('is_booked', false)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        return response()->json($slots);
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        
        $slots = BookingAvailability::where('date', $request->date)
            ->where('is_booked', false)
            ->get(['time']);

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
            'skillLevel' => 'required|string',
            'paymentMethod' => 'required|in:stripe,paystack',
        ]);

        // Check availability
        $availability = BookingAvailability::where('date', $request->date)
            ->where('time', $request->time)
            ->first();

        if (!$availability || $availability->is_booked) {
            return response()->json(['error' => 'This time slot is no longer available.'], 422);
        }

        $reference = Str::uuid()->toString();

        $booking = GuestBooking::create([
            'name' => $request->name,
            'email' => $request->email,
            'date' => $request->date,
            'time' => $request->time,
            'focus' => $request->focus,
            'skill_level' => $request->skillLevel,
            'payment_method' => $request->paymentMethod,
            'payment_status' => 'pending',
        ]);

        if ($request->paymentMethod === 'stripe') {
            return $this->initiateStripe($booking);
        } else {
            return $this->initiatePaystack($booking, $reference);
        }
    }

    private function initiateStripe(GuestBooking $booking)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'One-on-One Piano Lesson with Kingsley Khord',
                        ],
                        'unit_amount' => 10 * 100, // $10.00
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
                'amount' => 15000 * 100, // ₦15,000.00 in kobo
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

        // Send email notification (Link will be added manually by admin later)
        Mail::to($booking->email)->send(new GuestBookingConfirmed($booking));
    }
}
