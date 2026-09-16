@extends('layouts.admin')

@section('content')
@php
    $tab = request('tab', 'general');
@endphp
<main class="flex-1 p-4 sm:p-6" x-data="{ tab: '{{ $tab }}' }">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Payment Settings</h2>
        <p class="text-sm text-gray-500">Manage payment modes, API keys, webhooks, and subscription plan IDs.</p>
    </header>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-wrap gap-2 mb-6">
        @foreach([
            'general' => 'General',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'paystack' => 'Paystack',
            'webhooks' => 'Webhooks',
            'plans' => 'Plans',
        ] as $key => $label)
            <button type="button" @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Credentials form (general / stripe / paypal / paystack / webhooks) --}}
    <form method="POST" action="{{ route('admin.payment-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="tab" :value="tab">

        <div x-show="tab === 'general'" x-cloak class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">General</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Mode</label>
                    <select name="payment_mode" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm">
                        <option value="test" @selected(($settings['payment.mode'] ?? 'test') === 'test')>Test / Sandbox</option>
                        <option value="live" @selected(($settings['payment.mode'] ?? '') === 'live')>Live</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Controls which Stripe/Paystack keys are used app-wide.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">PayPal Mode</label>
                    <select name="paypal_mode" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm">
                        <option value="sandbox" @selected(($settings['paypal.mode'] ?? 'sandbox') === 'sandbox')>Sandbox</option>
                        <option value="live" @selected(($settings['paypal.mode'] ?? '') === 'live')>Live</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">PayPal Currency</label>
                    <input type="text" name="paypal_currency" value="{{ $settings['paypal.currency'] ?? 'USD' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm uppercase" maxlength="3">
                </div>
                <div class="flex items-end pb-1">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="fake_guest_payments" value="1"
                            @checked(($settings['payment.fake_guest_payments'] ?? '0') === '1')
                            class="rounded border-gray-300">
                        Fake guest booking payments (local testing only)
                    </label>
                </div>
            </div>
        </div>

        <div x-show="tab === 'stripe'" x-cloak class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">Stripe Keys</h3>
            <p class="text-xs text-gray-400">Leave secret fields blank to keep the current value.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Test Publishable Key</label>
                    <input type="text" name="stripe_test_key" value="{{ $settings['stripe.test_key'] ?? '' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Test Secret Key</label>
                    <input type="password" name="stripe_test_secret" placeholder="{{ $masks['stripe.test_secret'] ?: 'sk_test_…' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono" autocomplete="new-password">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Live Publishable Key</label>
                    <input type="text" name="stripe_live_key" value="{{ $settings['stripe.live_key'] ?? '' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Live Secret Key</label>
                    <input type="password" name="stripe_live_secret" placeholder="{{ $masks['stripe.live_secret'] ?: 'sk_live_…' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono" autocomplete="new-password">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Webhook Signing Secret</label>
                    <input type="password" name="stripe_webhook_secret" placeholder="{{ $masks['stripe.webhook_secret'] ?: 'whsec_…' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div x-show="tab === 'paypal'" x-cloak class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">PayPal REST API</h3>
            <p class="text-xs text-gray-400">Use credentials from Apps &amp; Credentials → REST API apps (not NVP/SOAP).</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sandbox Client ID</label>
                    <input type="text" name="paypal_test_client_id" value="{{ $settings['paypal.test_client_id'] ?? '' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sandbox Secret</label>
                    <input type="password" name="paypal_test_client_secret" placeholder="{{ $masks['paypal.test_client_secret'] ?: 'Secret' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono" autocomplete="new-password">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Live Client ID</label>
                    <input type="text" name="paypal_live_client_id" value="{{ $settings['paypal.live_client_id'] ?? '' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Live Secret</label>
                    <input type="password" name="paypal_live_client_secret" placeholder="{{ $masks['paypal.live_client_secret'] ?: 'Secret' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono" autocomplete="new-password">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Webhook ID</label>
                    <input type="text" name="paypal_webhook_id" value="{{ $settings['paypal.webhook_id'] ?? '' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono"
                        placeholder="WH-…">
                </div>
            </div>
        </div>

        <div x-show="tab === 'paystack'" x-cloak class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">Paystack Keys</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Test Secret Key</label>
                    <input type="password" name="paystack_test_secret_key" placeholder="{{ $masks['paystack.test_secret_key'] ?: 'sk_test_…' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono" autocomplete="new-password">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Live Secret Key</label>
                    <input type="password" name="paystack_live_secret_key" placeholder="{{ $masks['paystack.live_secret_key'] ?: 'sk_live_…' }}"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-mono" autocomplete="new-password">
                </div>
            </div>
        </div>

        <div x-show="tab === 'webhooks'" x-cloak class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">Webhook Endpoints</h3>
            <p class="text-sm text-gray-500">Paste these URLs into each provider’s webhook settings.</p>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400 mb-1">Stripe</p>
                    <code class="block bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm break-all">{{ $webhookUrls['stripe'] }}</code>
                    <p class="text-xs text-gray-400 mt-1">Set the signing secret under the Stripe tab.</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400 mb-1">PayPal</p>
                    <code class="block bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm break-all">{{ $webhookUrls['paypal'] }}</code>
                    <p class="text-xs text-gray-400 mt-1">Set the Webhook ID under the PayPal tab.</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400 mb-1">Paystack</p>
                    <code class="block bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm break-all">{{ $webhookUrls['paystack'] }}</code>
                </div>
            </div>
        </div>

        <div x-show="tab !== 'plans'" class="flex justify-end">
            <button type="submit" class="bg-gray-900 hover:bg-black text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                Save Payment Settings
            </button>
        </div>
    </form>

    {{-- Plans form (separate to keep validation simple) --}}
    <form method="POST" action="{{ route('admin.payment-settings.plans') }}" x-show="tab === 'plans'" x-cloak>
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-500">Subscription Plans</h3>
                <p class="text-xs text-gray-400 mt-1">Prices and gateway product / price IDs used for memberships.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100 bg-gray-50">
                            <th class="px-4 py-3">Plan</th>
                            <th class="px-4 py-3">USD</th>
                            <th class="px-4 py-3">EUR</th>
                            <th class="px-4 py-3">NGN</th>
                            <th class="px-4 py-3">Stripe Price ID</th>
                            <th class="px-4 py-3">Paystack Plan</th>
                            <th class="px-4 py-3">PayPal Product</th>
                            <th class="px-4 py-3">PayPal Plan USD</th>
                            <th class="px-4 py-3">PayPal Plan EUR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($plans as $i => $plan)
                            @php $paypalIds = is_array($plan->paypal_plan_ids) ? $plan->paypal_plan_ids : []; @endphp
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="hidden" name="plans[{{ $i }}][id]" value="{{ $plan->id }}">
                                    <div class="font-semibold text-gray-900 capitalize">{{ $plan->tier }}</div>
                                    <div class="text-xs text-gray-400 capitalize">{{ $plan->type }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" min="0" name="plans[{{ $i }}][price_usd]" value="{{ $plan->price_usd }}"
                                        class="w-24 rounded border border-gray-200 px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" min="0" name="plans[{{ $i }}][price_eur]" value="{{ $plan->price_eur }}"
                                        class="w-24 rounded border border-gray-200 px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" min="0" name="plans[{{ $i }}][price_ngn]" value="{{ $plan->price_ngn }}"
                                        class="w-28 rounded border border-gray-200 px-2 py-1.5 text-sm">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="plans[{{ $i }}][stripe_product_id]" value="{{ $plan->stripe_product_id }}"
                                        class="w-40 rounded border border-gray-200 px-2 py-1.5 text-xs font-mono" placeholder="price_…">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="plans[{{ $i }}][paystack_product_id]" value="{{ $plan->paystack_product_id }}"
                                        class="w-40 rounded border border-gray-200 px-2 py-1.5 text-xs font-mono" placeholder="PLN_…">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="plans[{{ $i }}][paypal_product_id]" value="{{ $plan->paypal_product_id }}"
                                        class="w-40 rounded border border-gray-200 px-2 py-1.5 text-xs font-mono" placeholder="PROD-…">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="plans[{{ $i }}][paypal_plan_usd]" value="{{ $paypalIds['USD'] ?? '' }}"
                                        class="w-40 rounded border border-gray-200 px-2 py-1.5 text-xs font-mono" placeholder="P-…">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="plans[{{ $i }}][paypal_plan_eur]" value="{{ $paypalIds['EUR'] ?? '' }}"
                                        class="w-40 rounded border border-gray-200 px-2 py-1.5 text-xs font-mono" placeholder="P-…">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-10 text-center text-gray-500">No plans found. Seed the plans table first.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($plans->isNotEmpty())
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-gray-900 hover:bg-black text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                        Save Plans
                    </button>
                </div>
            @endif
        </div>
    </form>
</main>
@endsection
