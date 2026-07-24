@extends('layouts.app')

@section('title', 'Checkout - Shop')

@section('content')
<div class="bg-gray-50 min-h-screen pt-32 pb-24 px-4">
    <div class="max-w-7xl mx-auto">


        <!-- Header -->
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Checkout</h1>
            <span class="flex items-center gap-1.5 text-sm text-gray-500">
                <i class="fa-solid fa-lock text-xs"></i>
                Secure Checkout
            </span>
        </div>
        <p class="text-gray-500 mb-8">Fill in your details and complete your order.</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

            <!-- Left Column -->
            <div class="space-y-6">

                <!-- Billing Details -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="flex items-center justify-center w-7 h-7 rounded-md bg-gray-900 text-white text-sm font-bold flex-shrink-0">1</span>
                        <h2 class="text-lg font-bold text-gray-900">Billing Details</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">First Name</label>
                            <input type="text" placeholder="Enter your first name"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Last Name</label>
                            <input type="text" placeholder="Enter your last name"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Country / Region</label>
                        <select class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-500 focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                            <option>Select your country</option>
                            <option>Nigeria</option>
                            <option>United States</option>
                            <option>United Kingdom</option>
                            <option>Canada</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                        <input type="email" placeholder="Enter your email address"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                        <p class="text-xs text-gray-400 mt-1.5">We'll send your order confirmation to this email.</p>
                    </div>

                    <div class="flex items-start gap-2 mt-5 text-xs text-gray-500">
                        <i class="fa-solid fa-shield-halved text-gray-400 mt-0.5"></i>
                        Your personal data is secure and will never be shared with third parties.
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="flex items-center justify-center w-7 h-7 rounded-md bg-gray-900 text-white text-sm font-bold flex-shrink-0">3</span>
                        <h2 class="text-lg font-bold text-gray-900">Payment Method</h2>
                    </div>
                    <p class="text-sm text-gray-500 mb-5 ml-10">Choose your preferred payment method</p>

                    <div class="space-y-3">
                        <a href="/order-confirmation" class="w-full flex items-center justify-center gap-2 bg-[#635bff] hover:bg-[#544cf0] text-white font-semibold py-3.5 rounded-lg transition">
                            Pay with <span class="font-bold text-lg">stripe</span>
                        </a>
                        <a href="/order-confirmation" class="w-full flex items-center justify-center gap-2 bg-[#FFD736] hover:bg-[#e6c22e] text-black font-semibold py-3.5 rounded-lg transition">
                            Pay with <i class="fa-brands fa-paypal text-lg"></i><span class="font-bold text-lg">PayPal</span>
                        </a>
                    </div>

                    <div class="flex items-center gap-2 mt-4 text-xs text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                        Secure payments powered by Stripe & PayPal
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-7 h-7 rounded-md bg-gray-900 text-white text-sm font-bold flex-shrink-0">2</span>
                        <h2 class="text-lg font-bold text-gray-900">Your Order</h2>
                    </div>
                    <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $cartCount }} Items</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($items as $item)
                        <div class="flex items-center gap-4 py-4">
                            @if(!empty($item['thumbnail']))
                                <img src="/{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-16 h-16 rounded-lg overflow-hidden relative flex-shrink-0"
                                    style="background: linear-gradient(135deg, {{ $item['from'] }}, {{ $item['to'] }});">
                                    <div class="absolute inset-0 flex items-center justify-center p-1 text-center">
                                        <span class="text-white font-extrabold uppercase text-[9px] leading-tight" style="text-shadow: 0 1px 4px rgba(0,0,0,0.6);">
                                            {{ $item['name'] }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-[15px] truncate">{{ $item['name'] }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $item['type'] }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-gray-900">${{ number_format($item['price'] * $item['qty'], 2) }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">x{{ $item['qty'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 mt-2 pt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-gray-900 font-medium">${{ number_format($subtotal, 2) }}</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 mt-4 pt-4 flex items-center justify-between">
                    <span class="font-bold text-gray-900 text-lg">Total</span>
                    <div class="text-right">
                        <span class="text-2xl font-extrabold text-gray-900">${{ number_format($total, 2) }}</span>
                        <div class="text-xs text-gray-400">USD</div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
                    <div class="text-center">
                        <div class="w-9 h-9 mx-auto rounded-full border border-gray-200 flex items-center justify-center mb-2">
                            <i class="fa-solid fa-shield-halved text-gray-700 text-sm"></i>
                        </div>
                        <p class="text-xs font-semibold text-gray-900">Secure Checkout</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Your payment is 100% secure and encrypted.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-9 h-9 mx-auto rounded-full border border-gray-200 flex items-center justify-center mb-2">
                            <i class="fa-solid fa-cloud-arrow-down text-gray-700 text-sm"></i>
                        </div>
                        <p class="text-xs font-semibold text-gray-900">Instant Access</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Get your files immediately after payment.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-9 h-9 mx-auto rounded-full border border-gray-200 flex items-center justify-center mb-2">
                            <i class="fa-regular fa-comment-dots text-gray-700 text-sm"></i>
                        </div>
                        <p class="text-xs font-semibold text-gray-900">24/7 Support</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">We're here to help you anytime you need.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
