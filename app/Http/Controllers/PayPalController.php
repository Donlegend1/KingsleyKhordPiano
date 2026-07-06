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
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    public function pay(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('register');
        }

        try {
            $plan = Plan::where('type', $request->duration)->where('tier', $request->tier)->first();
            if (!$plan) {
                return redirect()->back()->with('error', 'Selected plan not found.');
            }

            $response = $this->paypalService->purchase($user, $plan, $request);

            if ($response->isRedirect()) {
                $response->redirect();
            }
            return $response->getMessage();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function success(Request $request)
    {
        if ($request->input('paymentId') && $request->input('payerID')) {
            try {
                $success = $this->paypalService->completePurchase(
                    $request->input('paymentId'),
                    $request->input('payerID')
                );

                if ($success) {
                    return redirect()->route('my-library')->with('success', 'Payment successful and subscription activated!');
                }
                return redirect()->route('my-library')->with('error', 'Payment not successful');
            } catch (\Exception $e) {
                return redirect()->route('my-library')->with('error', $e->getMessage());
            }
        }

        return redirect()->route('my-library')->with('error', 'Payment is declined');
    }

    public function error()
    {
        return redirect()->route('my-library')->with('error', 'Payment is declined');
    }
}
