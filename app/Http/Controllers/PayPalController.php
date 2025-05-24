<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        // dd($request->all());
        try {
            $response = $this->gateway->purchase(array(
                'amount' => 200,
                'currency' => config('services.paypal.currency'),
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
                'user_id' => Auth::user()->id,
                'reference' => $arr['id'],
                'amount' => $arr['transactions'][0]['amount']['total'],
                'payment_method' =>'paypal',
                'status' => $arr['state'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return reduirect()->back()->with('success', 'payment successful');
        }
        return reduirect()->back()->with('error', 'payment not successful');
       }

       return reduirect()->back()->with('error', 'payment is declined');
    }

    function error()  {
        return reduirect()->back()->with('error', 'payment is declined');
    }
    
}
