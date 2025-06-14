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

        // dd($request->all());
        $user = Auth::user();
         if (!$user) {
            return redirect('register');
        }
        $reference = Str::uuid()->toString(); 

        DB::table('payments')->insert([
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $request->amount,
            'metadata' => json_encode($request->all()),
            'payment_method' =>'stripe',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = Auth::user();
        $user->metadata = $request->all();
        $user->payment_status = 'pending';
        $user->payment_method ='stripe';
        $user->premium = $request->tier === 'premium';
        $user->last_payment_reference = $reference;
        $user->last_payment_amount = $request->amount;
        $user->last_payment_at = now();
        $user->save();

        $stripe = new \Stripe\StripeClient($this->stripeKey);

        $checkout_session = $stripe->checkout->sessions->create([
          'line_items' => [[
            'price_data' => [
              'currency' => $request->currency,
              'product_data' => [
                'name' => $request->tier,
              ],
              'unit_amount' => $request->amount * 100,
            ],
            'quantity' => 1,
          ]],
          'mode' => 'payment',
          'success_url' => 'http://kingsleykhordpaino.test/stripe/success?session_id={CHECKOUT_SESSION_ID}&reference=' . $reference,
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
        try {
            $stripe = new \Stripe\StripeClient($this->stripeKey);
            $checkout_session = $stripe->checkout->sessions->retrieve(
                $request->input('session_id')
            );
    
            if ($checkout_session->status === 'complete') {
                 DB::table('payments')->where('reference', $request->reference)->update([
                    'status' => 'successful',
                    'updated_at' => now(),
                ]);
                 $user = Auth::user();
                $user->payment_status = 'successful';
                $user->save();

                return redirect()->route('home')->with('success', 'Payment Successful!');
            } else {
                 DB::table('payments')->where('reference', $reference)->update([
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);
                return redirect()->route('home')->with('error', 'Payment not verified.');
            }
        } catch (\Exception $e) {
            // Log the exception
            \Log::error($e->getMessage());
            return redirect()->route('home')->with('error', 'An error occurred while processing your payment.');
        }
    }

   
}
