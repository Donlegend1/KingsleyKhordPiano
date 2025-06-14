<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Unicodeveloper\Paystack\Facades\Paystack;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;

class PaymentController extends Controller
{
    
    public function initialize(Request $request)
    {
        $user = Auth::user(); // or pass user explicitly from request
    
        if (!$user) {
            return redirect('register');
        }
    
        $reference = Str::uuid()->toString(); 
       
        // Optional: Store transaction record
        DB::table('payments')->insert([
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $request->amount,
            'metadata' => json_encode($request->all()),
            'payment_method' =>'paystack',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = Auth::user();
        $user->metadata = $request->all();
        $user->payment_status = 'pending';
        $user->premium = $request->tier === 'premium';
        $user->payment_method ='paystack';
        $user->last_payment_reference = $reference;
        $user->last_payment_amount = $request->amount;
        $user->last_payment_at = now();
        $user->save();
    
        $fields = [
            'email' => $user->email,
            'amount' => $request->amount * 100,
            'reference' => $reference,
            'metadata' => json_encode(['user_id' => $user->id, 'payload' => $request->all()]),
            'callback_url' => route('payment.verify'), 
        ];
    
        $fields_string = http_build_query($fields);
    
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/initialize");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . config('services.paystack.secret_key'),
            "Cache-Control: no-cache",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $result = curl_exec($ch);
        curl_close($ch);
    
        $response = json_decode($result, true);

        // dd($response);
        if ($response['status'] && isset($response['data']['authorization_url'])) {
            return redirect()->away($response['data']['authorization_url']);
        }
    
        return response()->json(['error' => 'Unable to initialize payment'], 500);
    }
    

    public function handlePaystackCallback(Request $request)
    {
        $reference = $request->query('reference');
    
        if (!$reference) {
            return response()->json(['error' => 'No transaction reference supplied'], 400);
        }
    
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.paystack.secret_key'),
            'Cache-Control' => 'no-cache',
        ])->get("https://api.paystack.co/transaction/verify/{$reference}");
    
        if ($response->successful()) {
            $data = $response->json();
    
            if ($data['data']['status'] === 'success') {
                // ✅ Update payment record
                DB::table('payments')->where('reference', $reference)->update([
                    'status' => 'successful',
                    'updated_at' => now(),
                ]);
                $user = Auth::user();
                $user->payment_status = 'successful';
                $user->save();
    
                return redirect()->route('home')->with('success', 'Payment verified successfully');
            } else {
                // ❌ Update as failed
                DB::table('payments')->where('reference', $reference)->update([
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);
    
                return redirect()->route('home')->with('success', 'Payment not successfully');
            }
        }
    
        return response()->json(['error' => 'Verification failed'], 500);
    }
      

    public function redirectToStripe(Request $request)
{
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $session = StripeSession::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'product_data' => [
                'currency' => 'usd',
                'unit_amount' => 100 * 100,
                'product_data' => [
                    'name' => "monthly plan",
                ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => route('stripe.cancel'),
    ]);

    return redirect($session->url);
}

public function stripeSuccess(Request $request)
{
    // You can validate the session if needed
    return view('payment.success');
}

public function stripeCancel()
{
    return view('payment.cancel');
}
}

