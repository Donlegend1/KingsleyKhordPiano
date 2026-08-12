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
                    'headers' => $request->headers->all(),
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
        // Allow local/dev without webhook id configured.
        $webhookId = config('services.paypal.webhook_id') ?: env('PAYPAL_WEBHOOK_ID');
        if (! $webhookId) {
            Log::warning('PAYPAL_WEBHOOK_ID not set; skipping signature verification.');

            return app()->environment('local', 'development', 'testing');
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
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

            return strtoupper((string) data_get($result, 'verification_status')) === 'SUCCESS';
        } catch (\Throwable $e) {
            Log::error('PayPal webhook verify exception', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
