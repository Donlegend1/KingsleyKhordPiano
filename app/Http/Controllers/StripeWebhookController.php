<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload    = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event      = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
           logger()->error('Invalid payload');
            return response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
           logger()->error('Invalid signature');
            return response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->updateUserSubscription($subscription, $subscription->status);
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $this->handleInvoicePayment($invoice, true);
                break;

            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $this->handleInvoicePayment($invoice, false);
                break;

            case 'payment_intent.succeeded':
                $intent = $event->data->object;
                $this->recordPayment($intent, 'successful');
                break;

            case 'payment_intent.payment_failed':
                $intent = $event->data->object;
                $this->recordPayment($intent, 'failed');
                break;

            default:
                logger()->info("Unhandled Stripe event: {$event->type}");
        }

        return response('Webhook handled', Response::HTTP_OK);
    }

    /**
     * Handle checkout.session.completed
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        $userId   = $session->metadata->user_id ?? null;
        $tier     = $session->metadata->tier ?? null;
        $duration = $session->metadata->duration ?? 'monthly';
        $currency = $session->currency ?? 'eur';
        $amount   = ($session->amount_total ?? 0) / 100;

        if (!$userId || !$tier) {
            logger()->error("Missing metadata in checkout.session: " . json_encode($session->metadata));
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            logger()->warning("User not found for checkout session user_id={$userId}");
            return;
        }

        $this->recordPayment($session, 'successful', $userId, [
            'tier'     => $tier,
            'duration' => $duration,
            'currency' => $currency,
        ]);

        $user->subscription_tier       = $tier;
        $user->premium                 = stripos($tier, 'premium') !== false;
        if ($duration === 'monthly') {
            $user->subscription_expires_at = now()->addMonth();
        } elseif ($duration === 'quarterly') {
            $user->subscription_expires_at = now()->addMonths(3);
        } else {
            $user->subscription_expires_at = now()->addYear();
        }
        $user->subscription_status     = 'active';
        $user->save();

        // Create or update subscription record
        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'default'],
            [
                'stripe_id' => $session->subscription ?? 'stripe_' . $session->id,
                'stripe_status' => 'active',
                'status' => 'active',
                'ends_at' => $user->subscription_expires_at,
                'payment_method' => 'stripe',
            ]
        );

        logger()->info("Checkout session completed for user {$user->id}, tier={$tier}");
    }

    /**
     * Update or create subscription records
     */
    protected function updateUserSubscription($subscription, $status)
    {
        $user = User::where('stripe_id', $subscription->customer)->first();

        if ($user) {
            Subscription::updateOrCreate(
                [
                    'stripe_id' => $subscription->id,
                ],
                [
                    'user_id'       => $user->id,
                    'type'          => 'default',
                    'stripe_status' => $status,
                    'stripe_price'  => $subscription->items->data[0]->price->id ?? null,
                    'quantity'      => $subscription->items->data[0]->quantity ?? 1,
                    'trial_ends_at' => isset($subscription->trial_end)
                                        ? Carbon::createFromTimestamp($subscription->trial_end)
                                        : null,
                    'ends_at'       => isset($subscription->current_period_end)
                                        ? Carbon::createFromTimestamp($subscription->current_period_end)
                                        : null,
                ]
            );

            $user->last_payment_reference = $subscription->id;
            $user->payment_status         = $status === 'active' ? 'successful' : $status;
            $user->last_payment_amount    = isset($subscription->items->data[0]->price->unit_amount)
                ? $subscription->items->data[0]->price->unit_amount / 100
                : 0;

            $user->save();

           logger()->info("User {$user->id} subscription synced: {$status}");
        } else {
            logger()->warning("User not found for Stripe customer: {$subscription->customer}");
        }
    }


    /**
     * Handle invoice payments
     */
    protected function handleInvoicePayment($invoice, $success = true)
    {
        $status = $success ? 'successful' : 'failed';
        $this->recordPayment($invoice, $status, null, [
            'invoice_id' => $invoice->id,
        ]);

        $user = User::where('stripe_id', $invoice->customer)->first();
        if ($user) {
            $user->subscription_status = $success ? 'active' : 'past_due';
            $user->save();

            $msg = $success ? 'Payment succeeded' : 'Payment failed';
            logger()->info("{$msg} for user {$user->id}, invoice={$invoice->id}");
        }
    }

    /**
     * Record payment in your payments table
     */
    protected function recordPayment($object, $status, $userId = null, array $extra = [])
    {
        $reference = $object->id ?? null;
        $amount    = isset($object->amount_total)
            ? $object->amount_total / 100
            : (isset($object->amount_received) ? $object->amount_received / 100 : 0);
        $currency  = $object->currency ?? 'eur';

        $metadata = array_merge([
            'currency' => $currency,
        ], $extra);

        DB::table('payments')->updateOrInsert(
            ['reference' => $reference],
            [
                'user_id'        => $userId,
                'reference'      => $reference,
                'amount'         => $amount,
                'metadata'       => json_encode($metadata),
                'payment_method' => 'stripe',
                'notified_at'    => null,
                'starts_at'      => now(),
                'ends_at'        => isset($extra['duration']) && $extra['duration'] === 'monthly'
                    ? now()->addMonth()
                    : (isset($extra['duration']) && $extra['duration'] === 'quarterly'
                        ? now()->addMonths(3)
                        : now()->addYear()),
                'status'         => $status,
                'updated_at'     => now(),
            ]
        );
    }
}
