<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalWebhookController extends Controller
{
    public function __construct(protected PayPalService $paypalService)
    {
    }

    public function handle(Request $request)
    {
        $payload = $request->all();

        try {
            if (! $this->verifyWebhook($request)) {
                Log::warning('PayPal webhook verification failed', [
                    'event_type' => data_get($payload, 'event_type'),
                    'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                ]);

                return response()->json(['message' => 'Invalid signature'], 400);
            }

            $this->paypalService->handleWebhook($payload);

            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            Log::error('PayPal webhook handling failed', [
                'error' => $e->getMessage(),
                'event_type' => data_get($payload, 'event_type'),
            ]);

            return response()->json(['message' => 'Webhook error'], 500);
        }
    }

    protected function verifyWebhook(Request $request): bool
    {
        $eventMode = $this->eventMode($request);
        $webhookId = $this->webhookId($eventMode);

        if (! $webhookId) {
            Log::warning('PayPal webhook ID is not configured. Set it in Admin > Payment Settings or PAYPAL_WEBHOOK_ID / PAYPAL_SANDBOX_WEBHOOK_ID.', [
                'event_mode' => $eventMode,
                'app_mode' => config('paypal.mode'),
            ]);

            return app()->environment('local', 'development', 'testing');
        }

        try {
            $credentials = config('paypal');
            $credentials['mode'] = $eventMode;

            $provider = new PayPalClient;
            $provider->setApiCredentials($credentials);
            $provider->getAccessToken();

            $result = $provider->verifyWebHook([
                'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url' => $request->header('PAYPAL-CERT-URL'),
                'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                'webhook_id' => $webhookId,
                'webhook_event' => $request->all(),
            ]);

            $status = strtoupper((string) data_get($result, 'verification_status'));

            if ($status !== 'SUCCESS') {
                Log::warning('PayPal webhook signature was not SUCCESS', [
                    'verification_status' => $status ?: data_get($result, 'error.message', 'unknown'),
                    'event_mode' => $eventMode,
                ]);
            }

            return $status === 'SUCCESS';
        } catch (\Throwable $e) {
            Log::error('PayPal webhook verify exception', ['error' => $e->getMessage()]);

            return false;
        }
    }

    protected function eventMode(Request $request): string
    {
        $certUrl = strtolower((string) $request->header('PAYPAL-CERT-URL'));

        if (str_contains($certUrl, 'sandbox.paypal.com')) {
            return 'sandbox';
        }

        if (str_contains($certUrl, 'paypal.com')) {
            return 'live';
        }

        return config('paypal.mode') === 'live' ? 'live' : 'sandbox';
    }

    protected function webhookId(string $mode): ?string
    {
        $candidates = [
            config("paypal.{$mode}.webhook_id"),
            config('services.paypal.webhook_id'),
            $mode === 'live'
                ? env('PAYPAL_LIVE_WEBHOOK_ID', env('PAYPAL_WEBHOOK_ID'))
                : env('PAYPAL_SANDBOX_WEBHOOK_ID', env('PAYPAL_WEBHOOK_ID')),
        ];

        foreach ($candidates as $value) {
            if (filled($value)) {
                return (string) $value;
            }
        }

        return null;
    }
}
