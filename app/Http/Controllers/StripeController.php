<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    protected $stripeService;
    protected $stripeKey;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $this->stripeKey = config('services.stripe.secret');
    }

    /**
     * Create Payment Intent
     */
    public function createPaymentIntent(Request $request)
    {
        $subDetails = Subscription::find($request->plan);
        $user = Auth::user();
        $reference = Str::uuid()->toString(); 

        DB::table('payments')->insert([
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $subDetails->amount_dollar,
            'payment_method' =>'stripe',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stripe = new \Stripe\StripeClient($this->stripeKey);

        $checkout_session = $stripe->checkout->sessions->create([
          'line_items' => [[
            'price_data' => [
              'currency' => 'usd',
              'product_data' => [
                'name' => $subDetails->name,
              ],
              'unit_amount' => $subDetails->amount_dollar * 100,
            ],
            'quantity' => 1,
          ]],
          'mode' => 'payment',
          'success_url' => 'http://kingsleykhordpaino.test/stripe/success?session_id={CHECKOUT_SESSION_ID}',
          'cancel_url' => 'http://kingsleykhordpaino.test/stripe/cancel',
        ]);
        
        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);

    }

    /**
     * Retrieve Payment Intent
     */
    public function retrievePaymentIntent(Request $request)
    {
        // dd("here");
        try {
            $stripe = new \Stripe\StripeClient($this->stripeKey);
            $checkout_session = $stripe->checkout->sessions->retrieve(
                $request->input('session_id')
            );

            // dd($checkout_session);
    
            if ($checkout_session->status === 'complete') {
                // dd("here");
                DB::table('payments')->where('status', 'pending')->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);

                return redirect()->back()->with('success', 'Payment Successful!');
            } else {
                return redirect()->back()->with('error', 'Payment not verified.');
            }
        } catch (\Exception $e) {
            // Log the exception
            \Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing your payment.');
        }
    }

   
}
