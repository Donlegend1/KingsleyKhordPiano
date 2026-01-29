<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        if (!$signature) {
            return response()->json(['error' => 'No signature'], 400);
        }

        if ($signature !== hash_hmac('sha512', $payload, config('services.paystack.secret'))) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->event;

        switch ($event) {

            case 'subscription.create':
                $this->handleSubscriptionCreate($request->data);
                break;

            case 'invoice.payment_success':
                $this->handlePaymentSuccess($request->data);
                break;

            case 'invoice.payment_failed':
                $this->handlePaymentFailed($request->data);
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handleSubscriptionCreate($data)
    {
        Subscription::updateOrCreate(
            ['subscription_code' => $data['subscription_code']],
            [
                'plan_code' => $data['plan']['plan_code'],
                'email_token' => $data['email_token'],
                'status' => 'active',
            ]
        );
    }

    protected function handlePaymentSuccess($data)
    {
        Subscription::where('subscription_code', $data['subscription']['subscription_code'])
            ->update(['status' => 'active']);
    }

    protected function handlePaymentFailed($data)
    {
        Subscription::where('subscription_code', $data['subscription']['subscription_code'])
            ->update(['status' => 'past_due']);
    }
}
