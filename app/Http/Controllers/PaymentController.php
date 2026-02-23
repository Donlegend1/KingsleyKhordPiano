<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Paystack\PaystackSubscriptionService;
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

    public function initialize(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect('register');
            }
            
            $reference = Str::uuid()->toString();
            $plan =Plan::find($request->plan_id);
            Subscription::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'type' => 'default',
                'stripe_status' => 'pending',
                'payment_method' => 'paystack',

            ]);

            $payload = [
                'email' => $user->email,
                'plan' => $plan->paystack_product_id,
                'reference' => $reference,
                "amount"=> $plan->price_ngn *100, 
                'callback_url' => route('payment.verify'),
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ];

            $response = Http::withToken(config('services.paystack.secret_key'))
                ->acceptJson()
                ->post('https://api.paystack.co/transaction/initialize', $payload);

            if (!$response->successful()) {
                Log::error('Paystack initialize failed', [
                    'user_id' => $user->id,
                    'payload' => $payload,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'error' => 'Unable to initialize subscription',
                ], 500);
            }

            $data = $response->json();

            if (!($data['status'] ?? false) || empty($data['data']['authorization_url'])) {
                Log::warning('Paystack returned invalid initialize response', [
                    'user_id' => $user->id,
                    'response' => $data,
                ]);

                return response()->json([
                    'error' => 'Invalid payment response',
                ], 500);
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

    public function handlePaystackCallback(Request $request, PaystackSubscriptionService $service)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return response()->json(['error' => 'No transaction reference supplied'], 400);
        }

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (!$response->successful()) {
            return response()->json(['error' => 'Verification failed'], 500);
        }

        $data = $response->json()['data'];

        if ($data['status'] !== 'success') {
            // Mark subscription as failed
            Subscription::where('user_id', $data['metadata']['user_id'])
                ->update(['status' => 'failed']);

            return redirect()->route('home')->with('error', 'Payment failed');
        }

        $user =User::where('email', $data['email'])->first();
        $service->store($user, $data);

        return redirect()->route('home')->with('success', 'Subscription activated');
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

    public function manualPayment(ManualPaymentRequest $request)
    {
        $user = User::findOrFail($request->user_id);
        $reference = (string) Str::uuid();

        $startsAt = $request->starts_at ?? now();
        $endsAt = $request->ends_at ?? now()->addMonth();
        $reference = Str::uuid()->toString(); 

        DB::beginTransaction();
        try {
            $subscription = Subscription::where('user_id', $user->id)->first();

            if ($subscription) {
                $subscription->update([
                    'ends_at' => $endsAt,
                    'stripe_status' => 'active',
                    'stripe_price' => $request->plan_price_id ?? $subscription->stripe_price,
                    'quantity' => 1,
                ]);
            } else {
            DB::table('payments')->insert([
                'user_id' => $request->user_id,
                'reference' => $reference,
                'amount' => $request->amount,
                'metadata' => json_encode($request->all()),
                'payment_method' =>'Manual',
                'starts_at' => $startsAt,
                'notified_at' => null,
                'ends_at' =>  $endsAt,
                'status' => 'successful',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            }

            $user->update([
                'metadata' => $request->all(),
                'premium' => $request->premium === 'premium',
                'payment_method' => 'Manual',
                'last_payment_reference' => $reference,
                'last_payment_amount' => $request->amount,
                'payment_status' => 'successful',
                'last_payment_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                    'Subscription Updated successfully.',
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Manual Payment Failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

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
        $user = User::where('email', $data['email'])->first();
        Subscription::updateOrCreate(
            ['user_id' => $user->id ,],
            [
                'subscription_code' => $data['subscription_code'],
                'plan_code' => $data['plan']['plan_code'],
                'email_token' => $data['email_token'],
                'ends_at' => $data['period_end'],
                'status' => 'active',
            ]
        );
    }

    protected function onInvoicePaymentSuccess(array $data)
    {
        $subscriptionCode = $data['subscription']['subscription_code'];

        $subscription = Subscription::where('subscription_code', $subscriptionCode)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update(['status' => 'active']);

    }

    protected function onInvoicePaymentFailed(array $data)
    {
        $subscriptionCode = $data['subscription']['subscription_code'];

        $subscription = Subscription::where('subscription_code', $subscriptionCode)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update(['status' => 'past_due']);

        $subscription->user->update([
            'payment_status' => 'past_due',
        ]);
    }

    protected function onSubscriptionDisabled(array $data)
    {
        $subscriptionCode = $data['subscription_code'];

        $subscription = Subscription::where('subscription_code', $subscriptionCode)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update(['status' => 'cancelled']);

    }

}

