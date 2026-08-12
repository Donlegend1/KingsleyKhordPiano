<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PaymentSettingsController extends Controller
{
    /** Keys that are secrets — blank submit keeps the existing value. */
    protected array $secretKeys = [
        'stripe.test_secret',
        'stripe.live_secret',
        'stripe.webhook_secret',
        'paypal.test_client_secret',
        'paypal.live_client_secret',
        'paystack.test_secret_key',
        'paystack.live_secret_key',
    ];

    public function index()
    {
        $settings = Setting::allCached();

        $defaults = $this->defaultsFromEnv();
        foreach ($defaults as $key => $value) {
            if (! array_key_exists($key, $settings) || $settings[$key] === null || $settings[$key] === '') {
                $settings[$key] = $value;
            }
        }

        return view('admin.payment_settings.index', [
            'settings' => $settings,
            'plans' => Plan::orderBy('tier')->orderBy('type')->get(),
            'webhookUrls' => [
                'stripe' => url('/stripe/webhook'),
                'paypal' => url('/webhooks/paypal'),
                'paystack' => url('/webhooks/paystack'),
            ],
            'masks' => collect($this->secretKeys)->mapWithKeys(fn ($key) => [
                $key => $this->mask($settings[$key] ?? null),
            ])->all(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'payment_mode' => 'required|in:test,live',
            'paypal_mode' => 'required|in:sandbox,live',
            'paypal_currency' => 'required|string|max:3',
            'fake_guest_payments' => 'nullable|boolean',
            'stripe_test_key' => 'nullable|string|max:255',
            'stripe_live_key' => 'nullable|string|max:255',
            'stripe_test_secret' => 'nullable|string|max:255',
            'stripe_live_secret' => 'nullable|string|max:255',
            'stripe_webhook_secret' => 'nullable|string|max:255',
            'paypal_test_client_id' => 'nullable|string|max:255',
            'paypal_live_client_id' => 'nullable|string|max:255',
            'paypal_test_client_secret' => 'nullable|string|max:255',
            'paypal_live_client_secret' => 'nullable|string|max:255',
            'paypal_webhook_id' => 'nullable|string|max:255',
            'paystack_test_secret_key' => 'nullable|string|max:255',
            'paystack_live_secret_key' => 'nullable|string|max:255',
        ]);

        $pairs = [
            'payment.mode' => $request->payment_mode,
            'payment.fake_guest_payments' => $request->boolean('fake_guest_payments') ? '1' : '0',
            'stripe.test_key' => $request->stripe_test_key,
            'stripe.live_key' => $request->stripe_live_key,
            'paypal.mode' => $request->paypal_mode,
            'paypal.currency' => strtoupper($request->paypal_currency),
            'paypal.test_client_id' => $request->paypal_test_client_id,
            'paypal.live_client_id' => $request->paypal_live_client_id,
            'paypal.webhook_id' => $request->paypal_webhook_id,
        ];

        // Secrets: only overwrite when a new value is provided.
        foreach ([
            'stripe.test_secret' => 'stripe_test_secret',
            'stripe.live_secret' => 'stripe_live_secret',
            'stripe.webhook_secret' => 'stripe_webhook_secret',
            'paypal.test_client_secret' => 'paypal_test_client_secret',
            'paypal.live_client_secret' => 'paypal_live_client_secret',
            'paystack.test_secret_key' => 'paystack_test_secret_key',
            'paystack.live_secret_key' => 'paystack_live_secret_key',
        ] as $settingKey => $input) {
            $value = $request->input($input);
            if (filled($value)) {
                $pairs[$settingKey] = $value;
            }
        }

        // Non-secret public keys can be cleared intentionally.
        foreach (['stripe.test_key', 'stripe.live_key', 'paypal.test_client_id', 'paypal.live_client_id', 'paypal.webhook_id'] as $key) {
            if (array_key_exists($key, $pairs) && $pairs[$key] === null) {
                unset($pairs[$key]);
            }
        }

        Setting::setMany($pairs);

        // Allow explicit empty webhook id clear via checkbox? Skip for now.

        Setting::applyPaymentConfig();
        Artisan::call('config:clear');

        return redirect()
            ->route('admin.payment-settings.index', ['tab' => $request->input('tab', 'general')])
            ->with('success', 'Payment settings saved.');
    }

    public function updatePlans(Request $request)
    {
        $request->validate([
            'plans' => 'required|array',
            'plans.*.id' => 'required|integer|exists:plans,id',
            'plans.*.price_usd' => 'nullable|numeric|min:0',
            'plans.*.price_eur' => 'nullable|numeric|min:0',
            'plans.*.price_ngn' => 'nullable|numeric|min:0',
            'plans.*.stripe_product_id' => 'nullable|string|max:255',
            'plans.*.paystack_product_id' => 'nullable|string|max:255',
            'plans.*.paypal_product_id' => 'nullable|string|max:255',
            'plans.*.paypal_plan_usd' => 'nullable|string|max:255',
            'plans.*.paypal_plan_eur' => 'nullable|string|max:255',
        ]);

        foreach ($request->plans as $row) {
            $plan = Plan::find($row['id']);
            if (! $plan) {
                continue;
            }

            $paypalPlanIds = is_array($plan->paypal_plan_ids) ? $plan->paypal_plan_ids : [];
            if (array_key_exists('paypal_plan_usd', $row)) {
                if (filled($row['paypal_plan_usd'])) {
                    $paypalPlanIds['USD'] = $row['paypal_plan_usd'];
                } else {
                    unset($paypalPlanIds['USD']);
                }
            }
            if (array_key_exists('paypal_plan_eur', $row)) {
                if (filled($row['paypal_plan_eur'])) {
                    $paypalPlanIds['EUR'] = $row['paypal_plan_eur'];
                } else {
                    unset($paypalPlanIds['EUR']);
                }
            }

            $plan->update([
                'price_usd' => $row['price_usd'] ?? $plan->price_usd,
                'price_eur' => $row['price_eur'] ?? $plan->price_eur,
                'price_ngn' => $row['price_ngn'] ?? $plan->price_ngn,
                'stripe_product_id' => $row['stripe_product_id'] ?? null,
                'paystack_product_id' => $row['paystack_product_id'] ?? null,
                'paypal_product_id' => $row['paypal_product_id'] ?? null,
                'paypal_plan_ids' => $paypalPlanIds ?: null,
            ]);
        }

        return redirect()
            ->route('admin.payment-settings.index', ['tab' => 'plans'])
            ->with('success', 'Subscription plans updated.');
    }

    protected function defaultsFromEnv(): array
    {
        return [
            'payment.mode' => env('PAYMENT_MODE', 'test'),
            'payment.fake_guest_payments' => env('FAKE_GUEST_PAYMENTS', false) ? '1' : '0',
            'stripe.test_key' => env('STRIPE_TEST_KEY', env('STRIPE_KEY')),
            'stripe.test_secret' => env('STRIPE_TEST_SECRET'),
            'stripe.live_key' => env('STRIPE_LIVE_KEY', env('STRIPE_KEY')),
            'stripe.live_secret' => env('STRIPE_LIVE_SECRET', env('STRIPE_SECRET')),
            'stripe.webhook_secret' => env('STRIPE_WEBHOOK_SECRET', env('STRIPE__WEBHOOK_SECRET')),
            'paypal.mode' => env('PAYPAL_MODE', 'sandbox'),
            'paypal.currency' => env('PAYPAL_CURRENCY', 'USD'),
            'paypal.test_client_id' => env('PAYPAL_TEST_CLIENT_ID', env('PAYPAL_SANDBOX_CLIENT_ID')),
            'paypal.test_client_secret' => env('PAYPAL_TEST_CLIENT_SECRET', env('PAYPAL_SANDBOX_CLIENT_SECRET')),
            'paypal.live_client_id' => env('PAYPAL_LIVE_CLIENT_ID'),
            'paypal.live_client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', env('PAYPAL_lIVE_CLIENT_SECRET')),
            'paypal.webhook_id' => env('PAYPAL_WEBHOOK_ID'),
            'paystack.test_secret_key' => env('PAYSTACK_TEST_SECRET_KEY', env('PAYSTACK_SECRET_KEY')),
            'paystack.live_secret_key' => env('PAYSTACK_LIVE_SECRET_KEY'),
        ];
    }

    protected function mask(?string $value): string
    {
        if (! $value) {
            return '';
        }

        $len = strlen($value);
        if ($len <= 8) {
            return str_repeat('•', $len);
        }

        return substr($value, 0, 4).str_repeat('•', max(4, $len - 8)).substr($value, -4);
    }
}
