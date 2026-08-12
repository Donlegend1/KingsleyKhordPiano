<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value]
        );

        Cache::forget('app.settings');
    }

    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            static::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        Cache::forget('app.settings');
    }

    /**
     * @return array<string, string|null>
     */
    public static function allCached(): array
    {
        if (! static::tableReady()) {
            return [];
        }

        return Cache::remember('app.settings', 3600, function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    public static function tableReady(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Overlay DB payment settings onto Laravel config so existing services keep working.
     */
    public static function applyPaymentConfig(): void
    {
        if (! static::tableReady()) {
            return;
        }

        $s = static::allCached();
        if ($s === []) {
            return;
        }

        $mode = $s['payment.mode'] ?? config('services.payment_mode', env('PAYMENT_MODE', 'test'));
        $isLive = $mode === 'live';

        config([
            'services.payment_mode' => $mode,
            'services.fake_guest_payments' => filter_var(
                $s['payment.fake_guest_payments'] ?? config('services.fake_guest_payments'),
                FILTER_VALIDATE_BOOLEAN
            ),
        ]);

        $stripeKey = $isLive
            ? ($s['stripe.live_key'] ?? null)
            : ($s['stripe.test_key'] ?? null);
        $stripeSecret = $isLive
            ? ($s['stripe.live_secret'] ?? null)
            : ($s['stripe.test_secret'] ?? null);
        $stripeWebhook = $s['stripe.webhook_secret'] ?? null;

        if ($stripeKey) {
            config([
                'services.stripe.key' => $stripeKey,
                'cashier.key' => $stripeKey,
                'stripe.public_key' => $stripeKey,
            ]);
        }

        if ($stripeSecret) {
            config([
                'services.stripe.secret' => $stripeSecret,
                'cashier.secret' => $stripeSecret,
                'stripe.secret_key' => $stripeSecret,
            ]);
        }

        if ($stripeWebhook) {
            config([
                'services.stripe.webhook_secret' => $stripeWebhook,
                'cashier.webhook.secret' => $stripeWebhook,
            ]);
        }

        // Keep legacy spaced key working if anything still reads it.
        if ($stripeWebhook) {
            config(['services.stripe. webhook_secret ' => $stripeWebhook]);
        }

        $paypalMode = $s['paypal.mode'] ?? config('paypal.mode', 'sandbox');
        $paypalIsLive = $paypalMode === 'live';

        config([
            'paypal.mode' => $paypalIsLive ? 'live' : 'sandbox',
            'paypal.currency' => $s['paypal.currency'] ?? config('paypal.currency', 'USD'),
            'services.paypal.currency' => $s['paypal.currency'] ?? config('services.paypal.currency', 'USD'),
            'services.paypal.test_mode' => ! $paypalIsLive,
        ]);

        if (! empty($s['paypal.test_client_id'])) {
            config(['paypal.sandbox.client_id' => $s['paypal.test_client_id']]);
        }
        if (! empty($s['paypal.test_client_secret'])) {
            config(['paypal.sandbox.client_secret' => $s['paypal.test_client_secret']]);
        }
        if (! empty($s['paypal.live_client_id'])) {
            config(['paypal.live.client_id' => $s['paypal.live_client_id']]);
        }
        if (! empty($s['paypal.live_client_secret'])) {
            config(['paypal.live.client_secret' => $s['paypal.live_client_secret']]);
        }

        if (! empty($s['paypal.webhook_id'])) {
            // Controllers currently read env(); also expose via config for future use.
            config(['services.paypal.webhook_id' => $s['paypal.webhook_id']]);
        }

        $activePaypalId = $paypalIsLive
            ? config('paypal.live.client_id')
            : config('paypal.sandbox.client_id');
        $activePaypalSecret = $paypalIsLive
            ? config('paypal.live.client_secret')
            : config('paypal.sandbox.client_secret');

        if ($activePaypalId) {
            config(['services.paypal.client_id' => $activePaypalId]);
        }
        if ($activePaypalSecret) {
            config(['services.paypal.secret' => $activePaypalSecret]);
        }

        $paystackSecret = $isLive
            ? ($s['paystack.live_secret_key'] ?? null)
            : ($s['paystack.test_secret_key'] ?? null);

        if ($paystackSecret) {
            config(['services.paystack.secret_key' => $paystackSecret]);
        }
    }
}
