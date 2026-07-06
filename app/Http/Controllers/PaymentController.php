<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Paystack\PaystackSubscriptionService;
use App\Services\PaystackService;
use App\Services\ManualPaymentService;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use App\Http\Requests\ManualPaymentRequest;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function initialize(Request $request, PaystackService $paystackService)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect('register');
            }
            
            $plan = Plan::find($request->plan_id);
            if (!$plan) {
                return redirect()->back()->with('error', 'Selected plan not found.');
            }

            $res = $paystackService->initialize($user, $plan, $request->currency);
            $response = $res['response'];
            $payload = $res['payload'];

            if (!$response->successful()) {
                $errData = $response->json();
                Log::error('Paystack initialize failed', [
                    'user_id' => $user->id,
                    'payload' => $payload,
                    'status' => $response->status(),
                    'response' => $errData,
                ]);

                $msg = $errData['message'] ?? 'Unable to initialize subscription';
                return redirect()->back()->with('error', "Paystack initialization failed: {$msg}");
            }

            $data = $response->json();

            if (!($data['status'] ?? false) || empty($data['data']['authorization_url'])) {
                Log::warning('Paystack returned invalid initialize response', [
                    'user_id' => $user->id,
                    'response' => $data,
                ]);

                return redirect()->back()->with('error', 'Paystack returned an invalid response.');
            }

            return redirect()->away($data['data']['authorization_url']);

        } catch (\Throwable $e) {
            Log::critical('Paystack subscription initialization exception', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Unable to initialize subscription',
            ], 500);
        }
    }

    public function handlePaystackCallback(Request $request, PaystackService $paystackService, PaystackSubscriptionService $service)
    {
        $reference = $request->query('reference');
        logger()->info('Paystack callback received', [
            'user_id' => Auth::id(),
            'reference' => $reference,
            'all' => $request->all(),
        ]);

        if (!$reference) {
            return response()->json(['error' => 'No transaction reference supplied'], 400);
        }

        $response = $paystackService->verify($reference);
        logger()->info('Paystack verification response', [
            'user_id' => Auth::id(),
            'reference' => $reference,
            'response' => $response->json(),
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Verification failed'], 500);
        }

        $data = $response->json()['data'];

        if ($data['status'] !== 'success') {
            // Mark subscription as failed
            Subscription::where('user_id', $data['metadata']['user_id'])
                ->update(['stripe_status' => 'past_due']);

            return redirect()->route('home')->with('error', 'Payment failed');
        }

        $user = User::where('email', $data['email'])->first();
        $service->store($user, $data);

        return redirect()->route('home')->with('success', 'Subscription activated');
    }

    public function directCheckout(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect('register');
            }

            $planId = $request->input('plan_id');
            if (!$planId) {
                return redirect('/member/plan')->with('error', 'Missing plan choice.');
            }

            $plan = Plan::find($planId);
            if (!$plan) {
                return redirect('/member/plan')->with('error', 'Selected plan not found.');
            }

            $paymentMethod = $request->input('payment_method');
            if (!$paymentMethod) {
                $currency = $request->input('currency', 'EUR');
                $paymentMethod = ($currency === 'NGN') ? 'paystack' : 'stripe';
                $request->merge(['payment_method' => $paymentMethod]);
            }

            if ($paymentMethod === 'stripe') {
                return app(\App\Http\Controllers\StripeController::class)->checkout($request);
            } elseif ($paymentMethod === 'paystack') {
                return $this->initialize($request, app(PaystackService::class));
            } elseif ($paymentMethod === 'paypal') {
                return app(\App\Http\Controllers\PayPalController::class)->pay($request);
            }

            return redirect('/member/plan')->with('error', 'Invalid payment method.');
        } catch (\Throwable $e) {
            Log::error('Direct checkout failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect('/member/plan')->with('error', 'Direct checkout failed: ' . $e->getMessage());
        }
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
        return view('payment.success');
    }

    public function stripeCancel()
    {
        return view('payment.cancel');
    }

    public function manualPayment(ManualPaymentRequest $request, ManualPaymentService $service)
    {
        try {
            $service->process($request);
            return response()->json([
                'success' => true,
                'message' => 'Subscription updated successfully.',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Manual payment failed. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function handlePaystackWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        if (!$signature) {
            return response()->json(['message' => 'Missing signature'], 400);
        }

        if ($signature !== hash_hmac('sha512', $payload, config('services.paystack.secret_key'))) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = $request->event;
        $data  = $request->data;

        try {
            switch ($event) {

                case 'subscription.create':
                    $this->onSubscriptionCreated($data);
                    break;

                case 'invoice.payment_success':
                    $this->onInvoicePaymentSuccess($data);
                    break;

                case 'invoice.payment_failed':
                    $this->onInvoicePaymentFailed($data);
                    break;

                case 'subscription.disable':
                    $this->onSubscriptionDisabled($data);
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (\Throwable $e) {
            logger()->error('Paystack Webhook Error', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);

            // Paystack expects 200 to stop retries
            return response()->json(['status' => 'error'], 200);
        }
    }

    protected function onSubscriptionCreated(array $data)
    {
        logger()->info(['web hook data' => $data]);
        $email = $data['customer']['email'] ?? $data['email'] ?? null;
        $user = User::where('email', $email)->first();
        
        if ($user) {
            Subscription::updateOrCreate(
                ['user_id' => $user->id, 'type' => 'default'],
                [
                    'stripe_id' => 'ps_' . $data['subscription_code'],
                    'subscription_code' => $data['subscription_code'],
                    'plan_code' => $data['plan']['plan_code'] ?? null,
                    'email_token' => $data['email_token'] ?? null,
                    'ends_at' => \Carbon\Carbon::parse($data['next_payment_date'] ?? '+1 month'),
                    'stripe_status' => 'active',
                    'payment_method' => 'paystack',
                ]
            );
        }
    }

    protected function onInvoicePaymentSuccess(array $data)
    {
        $subscriptionCode = $data['subscription']['subscription_code'] ?? $data['subscription_code'] ?? null;
        if (!$subscriptionCode) return;

        $subscription = Subscription::where('subscription_code', $subscriptionCode)->first();

        if ($subscription) {
            $subscription->update([
                'stripe_status' => 'active',
                'ends_at' => \Carbon\Carbon::parse($data['period_end'] ?? '+1 month')
            ]);
        }
    }

    protected function onInvoicePaymentFailed(array $data)
    {
        $subscriptionCode = $data['subscription']['subscription_code'] ?? $data['subscription_code'] ?? null;
        if (!$subscriptionCode) return;

        $subscription = Subscription::where('subscription_code', $subscriptionCode)->first();

        if ($subscription) {
            $subscription->update(['stripe_status' => 'past_due']);

            $subscription->user->update([
                'payment_status' => 'past_due',
            ]);
        }
    }

    protected function onSubscriptionDisabled(array $data)
    {
        $subscriptionCode = $data['subscription_code'] ?? null;
        if (!$subscriptionCode) return;

        $subscription = Subscription::where('subscription_code', $subscriptionCode)->first();

        if ($subscription) {
            $subscription->update(['stripe_status' => 'canceled']);
        }
    }

}

