<?php

namespace App\Http\Controllers;

use App\Models\ShopOrder;
use App\Support\ShopCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\StripeClient;
use Throwable;

class ShopCheckoutController extends Controller
{
    public function index()
    {
        $items = CartController::hydrate();

        if (empty($items)) {
            return redirect('/cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($items)->sum(fn ($i) => $i['price'] * $i['qty']);
        $user = Auth::user();

        return view('checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => 0,
            'total' => $subtotal,
            'cartCount' => CartController::count(),
            'billing' => [
                'first_name' => old('first_name', $user?->first_name ?? ''),
                'last_name' => old('last_name', $user?->last_name ?? ''),
                'email' => old('email', $user?->email ?? ''),
                'country' => old('country', ''),
            ],
        ]);
    }

    public function pay(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'country' => 'required|string|max:100',
            'payment_method' => 'required|in:stripe,paypal',
        ]);

        $items = CartController::hydrate();
        if (empty($items)) {
            return redirect('/cart')->with('error', 'Your cart is empty.');
        }

        $total = round(collect($items)->sum(fn ($i) => $i['price'] * $i['qty']), 2);
        if ($total <= 0) {
            return redirect('/cart')->with('error', 'Cart total must be greater than zero.');
        }

        $reference = (string) Str::uuid();
        $pending = [
            'reference' => $reference,
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'country' => $request->country,
            'payment_method' => $request->payment_method,
            'currency' => 'USD',
            'total' => $total,
            'items' => $items,
        ];

        Cache::put($this->pendingKey($reference), $pending, now()->addHours(6));

        ShopOrder::updateOrCreate(
            ['checkout_reference' => $reference],
            [
                'user_id' => Auth::id(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'country' => $request->country,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'total' => $total,
                'currency' => 'USD',
                'items' => $items,
            ]
        );

        try {
            return $request->payment_method === 'stripe'
                ? $this->startStripeCheckout($pending)
                : $this->startPayPalCheckout($pending);
        } catch (Throwable $e) {
            Log::error('Shop checkout failed', [
                'method' => $request->payment_method,
                'error' => $e->getMessage(),
            ]);

            return redirect('/checkout')->with('error', 'Unable to start payment. Please try again.');
        }
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        $reference = $request->query('reference');

        if (! $sessionId || ! $reference) {
            return redirect('/checkout')->with('error', 'Missing Stripe payment reference.');
        }

        $pending = Cache::get($this->pendingKey($reference));
        if (! $pending) {
            return redirect('/cart')->with('error', 'This checkout session has expired. Please try again.');
        }

        try {
            $stripe = new StripeClient($this->stripeSecret());
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if (($session->payment_status ?? null) !== 'paid' && ($session->status ?? null) !== 'complete') {
                return redirect('/checkout')->with('error', 'Stripe payment was not completed.');
            }

            $this->completeOrder($pending, (string) $sessionId);

            return redirect()->route('shop.order.confirmation');
        } catch (Throwable $e) {
            Log::error('Shop Stripe success failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect('/checkout')->with('error', 'Unable to verify Stripe payment.');
        }
    }

    public function paypalSuccess(Request $request)
    {
        $orderId = $request->query('token') ?? $request->query('order_id');
        $reference = $request->query('reference');

        if (! $orderId || ! $reference) {
            return redirect('/checkout')->with('error', 'Missing PayPal payment reference.');
        }

        $pending = Cache::get($this->pendingKey($reference));
        if (! $pending) {
            return redirect('/cart')->with('error', 'This checkout session has expired. Please try again.');
        }

        try {
            $provider = $this->paypalProvider();
            $capture = $provider->capturePaymentOrder($orderId);

            if ($error = data_get($capture, 'error')) {
                throw new \RuntimeException(
                    data_get($error, 'details.0.description')
                        ?? data_get($error, 'message')
                        ?? 'PayPal capture failed.'
                );
            }

            $status = strtoupper((string) data_get($capture, 'status', ''));
            if (! in_array($status, ['COMPLETED', 'APPROVED'], true)) {
                return redirect('/checkout')->with('error', "PayPal payment status: {$status}");
            }

            $this->completeOrder($pending, (string) data_get($capture, 'id', $orderId));

            return redirect()->route('shop.order.confirmation');
        } catch (Throwable $e) {
            Log::error('Shop PayPal success failed', [
                'reference' => $reference,
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return redirect('/checkout')->with('error', 'Unable to complete PayPal payment.');
        }
    }

    public function cancel()
    {
        return redirect('/checkout')->with('error', 'Payment was cancelled.');
    }

    public function confirmation()
    {
        $order = session('shop_order');

        if (! $order) {
            return redirect('/shop')->with('error', 'No completed order found.');
        }

        $items = collect($order['items'] ?? [])->map(fn ($item) => [
            'name' => $item['name'],
            'meta' => $item['type'],
            'price' => $item['price'] * $item['qty'],
            'qty' => $item['qty'],
            'downloadLabel' => ($item['type'] ?? '') === 'Plugin' ? 'Download Plugin' : 'Download File',
            'downloadUrl' => ShopCatalog::find($item['slug'])['download_url'] ?? null,
            'thumbnail' => $item['thumbnail'] ?? null,
            'from' => $item['from'] ?? '#111827',
            'to' => $item['to'] ?? '#4B5563',
        ])->all();

        $subtotal = collect($items)->sum('price');

        return view('order-confirmation', [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => 0,
            'total' => $order['total'] ?? $subtotal,
            'orderNumber' => $order['order_number'] ?? ('#KK-'.now()->format('Y').'-0000'),
            'orderDate' => $order['order_date'] ?? now()->format('F j, Y'),
            'email' => $order['email'] ?? '',
            'cartCount' => CartController::count(),
        ]);
    }

    protected function startStripeCheckout(array $pending)
    {
        $stripe = new StripeClient($this->stripeSecret());
        $lineItems = collect($pending['items'])->map(fn ($item) => [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $item['name'],
                    'description' => $item['type'],
                ],
                'unit_amount' => (int) round($item['price'] * 100),
            ],
            'quantity' => (int) $item['qty'],
        ])->values()->all();

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'customer_email' => $pending['email'],
            'line_items' => $lineItems,
            'success_url' => route('shop.checkout.stripe.success', [], true)
                .'?session_id={CHECKOUT_SESSION_ID}&reference='.$pending['reference'],
            'cancel_url' => route('shop.checkout.cancel', [], true),
            'metadata' => [
                'reference' => $pending['reference'],
                'type' => 'shop_cart',
                'email' => $pending['email'],
            ],
        ]);

        if (empty($session->url)) {
            throw new \RuntimeException('Stripe did not return a checkout URL.');
        }

        return redirect()->away($session->url);
    }

    protected function startPayPalCheckout(array $pending)
    {
        $provider = $this->paypalProvider();

        $items = collect($pending['items'])->map(fn ($item) => [
            'name' => substr($item['name'], 0, 127),
            'description' => substr($item['type'], 0, 127),
            'quantity' => (string) $item['qty'],
            'unit_amount' => [
                'currency_code' => 'USD',
                'value' => number_format($item['price'], 2, '.', ''),
            ],
        ])->values()->all();

        $order = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $pending['reference'],
                'custom_id' => $pending['reference'],
                'description' => 'Kingsley Khord Piano shop order',
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($pending['total'], 2, '.', ''),
                    'breakdown' => [
                        'item_total' => [
                            'currency_code' => 'USD',
                            'value' => number_format($pending['total'], 2, '.', ''),
                        ],
                    ],
                ],
                'items' => $items,
            ]],
            'application_context' => [
                'brand_name' => substr((string) config('app.name', 'Kingsley Khord Piano'), 0, 127),
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
                'return_url' => route('shop.checkout.paypal.success', ['reference' => $pending['reference']], true),
                'cancel_url' => route('shop.checkout.cancel', [], true),
            ],
        ]);

        if ($error = data_get($order, 'error')) {
            throw new \RuntimeException(
                data_get($error, 'details.0.description')
                    ?? data_get($error, 'message')
                    ?? 'Unable to create PayPal order.'
            );
        }

        $approveUrl = collect(data_get($order, 'links', []))
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (! $approveUrl) {
            throw new \RuntimeException('PayPal did not return an approval link.');
        }

        return redirect()->away($approveUrl);
    }

    protected function completeOrder(array $pending, string $paymentReference): void
    {
        $orderNumber = '#KK-'.now()->format('Y').'-'.random_int(1000, 9999);

        ShopOrder::updateOrCreate(
            ['checkout_reference' => $pending['reference']],
            [
                'order_number' => $orderNumber,
                'user_id' => $pending['user_id'] ?? null,
                'first_name' => $pending['first_name'],
                'last_name' => $pending['last_name'],
                'email' => $pending['email'],
                'country' => $pending['country'],
                'payment_method' => $pending['payment_method'],
                'payment_reference' => $paymentReference,
                'status' => 'paid',
                'total' => $pending['total'],
                'currency' => $pending['currency'] ?? 'USD',
                'items' => $pending['items'],
                'paid_at' => now(),
            ]
        );

        session([
            'shop_order' => [
                'order_number' => $orderNumber,
                'order_date' => now()->format('F j, Y'),
                'email' => $pending['email'],
                'first_name' => $pending['first_name'],
                'last_name' => $pending['last_name'],
                'country' => $pending['country'],
                'payment_method' => $pending['payment_method'],
                'payment_reference' => $paymentReference,
                'total' => $pending['total'],
                'currency' => $pending['currency'] ?? 'USD',
                'items' => $pending['items'],
            ],
            'cart' => [],
        ]);

        Cache::forget($this->pendingKey($pending['reference']));

        try {
            \App\Models\Payment::create([
                'user_id' => (string) ($pending['user_id'] ?? 'guest'),
                'reference' => $paymentReference,
                'amount' => $pending['total'],
                'status' => 'successful',
                'payment_method' => $pending['payment_method'],
                'starts_at' => now()->toDateString(),
                'ends_at' => now()->toDateString(),
                'metadata' => [
                    'type' => 'shop_cart',
                    'order_number' => $orderNumber,
                    'first_name' => $pending['first_name'],
                    'last_name' => $pending['last_name'],
                    'email' => $pending['email'],
                    'country' => $pending['country'],
                    'checkout_reference' => $pending['reference'],
                    'items' => collect($pending['items'])->map(fn ($i) => [
                        'slug' => $i['slug'],
                        'qty' => $i['qty'],
                        'price' => $i['price'],
                    ])->values()->all(),
                ],
            ]);
        } catch (Throwable $e) {
            Log::warning('Shop payment record save failed', [
                'reference' => $paymentReference,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function paypalProvider(): PayPalClient
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $provider->setCurrency('USD');

        return $provider;
    }

    protected function stripeSecret(): string
    {
        $secret = config('services.stripe.secret')
            ?: config('cashier.secret')
            ?: config('stripe.secret_key');

        if (! $secret) {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        return $secret;
    }

    protected function pendingKey(string $reference): string
    {
        return "shop_checkout:{$reference}";
    }
}
