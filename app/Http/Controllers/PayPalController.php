<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Plan;

class PayPalController extends Controller
{

private $gateway;

    protected $paypalService;

    public function __construct(Type $var = null)
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId(config('services.paypal.client_id'));
        $this->gateway->setSecret(config('services.paypal.secret'));
        $this->gateway->setTestMode(config('services.paypal.test_mode'));
    }

    function pay(Request $request)
    {
        
         $user = Auth::user();
         if (!$user) {
            return redirect('register');
        }
        try {
             $reference = Str::uuid()->toString(); 

             $plan = Plan::where('type', $request->duration)->where('tier', $request->tier)->first();
       

        DB::table('payments')->insert([
            'user_id' => $user->id,
            'reference' => $reference,
            'amount' => $plan->price_usd,
            'metadata' => json_encode($request->all()),
            'payment_method' =>'paypal',
            'status' => 'pending',
            'notified_at' => null,
            'starts_at' => now(),
            'ends_at' =>  $request->duration ==="monthly" ? now()->addMonth(1) : now()->addYear(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

            $user->metadata = $request->all();
            $user->payment_method ='paypal';
            $user->premium = $request->tier === 'premium';
            $user->last_payment_reference = $reference;
            $user->last_payment_amount = $plan->price_usd;
            $user->last_payment_at = now();
            $user->save();

            $response = $this->gateway->purchase(array(
                'amount' => $plan->price_usd,
                'currency' => $request->currency,
                'returnUrl' => url('paypal/success'),
                'cancelUrl' => url('paypal/cancel'),
            ))->send();
    
            if ($response->isRedirect()) {
                $response->redirect();
            }
            return $response->getMessage();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    function success(Request $request)  {
       if ($request->input('paymentId') && $request->input('payerID')) {
        $transaction = $this->gateway->completePurchase(array(
            'payer_id' => $request->input('payerID'),
            'transactionReference' => $request->input('paymentId')
       
        ))->send();

        if ($transaction->isSuccessful()) {
            $arr = $transaction->getData();

            DB::table('payments')->insert([
                'user_id' => $user->id,
                'reference' => $arr['id'],
                'amount' => $arr['transactions'][0]['amount']['total'],
                'payment_method' =>'paypal',
                'status' => $arr['state'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'payment successful');
        }
        return redirect()->back()->with('error', 'payment not successful');
       }

       return redirect()->back()->with('error', 'payment is declined');
    }

    function error()  {
        return redirect()->back()->with('error', 'payment is declined');
    }
    
}
