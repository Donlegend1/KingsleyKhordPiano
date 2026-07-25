@extends('layouts.app')

@section('title', 'Your Cart - Shop')

@section('content')
<div class="bg-white min-h-screen pt-32 pb-24 px-4">
    <div class="max-w-7xl mx-auto">


        <!-- Header -->
        <div class="flex items-center gap-3 mb-1">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Your Cart</h1>
            <span class="bg-gray-900 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $cartCount }} Items</span>
        </div>
        <p class="text-gray-500 mb-8">Review your items and proceed to checkout.</p>

        @if(count($items) === 0)
            <div class="text-center py-10">
                <i class="fa-solid fa-cart-shopping text-6xl text-gray-900"></i>

                <div class="border border-gray-200 rounded-lg py-4 mt-8 text-gray-600">
                    Your cart is currently empty.
                </div>

                <a href="/shop" class="inline-flex items-center justify-center bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 py-3 rounded-lg transition mt-6">
                    Return to shop
                </a>
            </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="hidden sm:grid grid-cols-[2fr_1fr_1fr_1fr_1fr_auto] gap-4 px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                        <span>Product</span>
                        <span>Type</span>
                        <span>Price</span>
                        <span>Quantity</span>
                        <span>Total</span>
                        <span></span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($items as $item)
                            <div class="grid grid-cols-1 sm:grid-cols-[2fr_1fr_1fr_1fr_1fr_auto] gap-4 items-center px-6 py-5">
                                <div class="flex items-center gap-4 min-w-0">
                                    @if(!empty($item['thumbnail']))
                                        <img src="/{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-16 h-16 rounded-lg overflow-hidden relative flex-shrink-0"
                                            style="background: linear-gradient(135deg, {{ $item['from'] }}, {{ $item['to'] }});">
                                            <div class="absolute bottom-0 left-0 right-0 h-3 flex">
                                                @for($i = 0; $i < 10; $i++)
                                                    <div class="flex-1 border-r border-black/30 bg-white/90"></div>
                                                @endfor
                                            </div>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-[15px] truncate">{{ $item['name'] }}</h3>
                                    </div>
                                </div>

                                <span class="inline-flex w-fit text-xs font-semibold px-2.5 py-1 rounded-md {{ $item['type'] === 'Plugin' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $item['type'] }}
                                </span>

                                <span class="text-gray-900 font-medium">${{ number_format($item['price'], 2) }}</span>

                                <div class="flex items-center gap-2 w-fit border border-gray-200 rounded-lg">
                                    <button class="cart-qty-btn w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-900 transition" data-slug="{{ $item['slug'] }}" data-qty="{{ $item['qty'] - 1 }}" aria-label="Decrease quantity">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                    <span class="w-6 text-center text-sm font-medium">{{ $item['qty'] }}</span>
                                    <button class="cart-qty-btn w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-900 transition" data-slug="{{ $item['slug'] }}" data-qty="{{ $item['qty'] + 1 }}" aria-label="Increase quantity">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </div>

                                <span class="text-gray-900 font-bold">${{ number_format($item['price'] * $item['qty'], 2) }}</span>

                                <button class="cart-remove-btn text-gray-400 hover:text-red-500 transition w-fit" data-remove-slug="{{ $item['slug'] }}" aria-label="Remove item">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <button id="clear-cart-btn" class="flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                        <i class="fa-regular fa-trash-can"></i>
                        Clear Cart
                    </button>
                </div>

                <!-- Perks -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 border border-gray-200 rounded-xl p-6">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-download text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Instant Download</p>
                            <p class="text-xs text-gray-500 mt-0.5">Get your files immediately after purchase.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-shield-halved text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Secure Payment</p>
                            <p class="text-xs text-gray-500 mt-0.5">Your payment information is 100% secure.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-arrows-rotate text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Free Updates</p>
                            <p class="text-xs text-gray-500 mt-0.5">Receive free updates for all purchased products.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-headset text-amber-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">24/7 Support</p>
                            <p class="text-xs text-gray-500 mt-0.5">We're here to help you anytime you need.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="border border-gray-200 rounded-xl p-6 sticky top-28">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Order Summary</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="text-gray-900 font-medium">${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 mt-4 pt-4 flex items-center justify-between">
                        <span class="font-bold text-gray-900">Total</span>
                        <div class="text-right">
                            <span class="text-2xl font-extrabold text-gray-900">${{ number_format($total, 2) }}</span>
                            <div class="text-xs text-gray-400">USD</div>
                        </div>
                    </div>

                    <a href="/checkout" class="w-full flex items-center justify-center gap-2 bg-[#FFD736] hover:bg-[#e6c22e] text-black font-bold py-3.5 rounded-lg transition mt-6">
                        <i class="fa-solid fa-lock text-sm"></i>
                        Proceed to Checkout
                    </a>

                    <div class="mt-6 text-center">
                        <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                            <div class="flex-1 border-t border-gray-100"></div>
                            Secure checkout with
                            <div class="flex-1 border-t border-gray-100"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <span class="h-9 rounded-md border border-gray-200 flex items-center justify-center">
                                <i class="fa-brands fa-cc-visa text-[#1a1f71] text-lg"></i>
                            </span>
                            <span class="h-9 rounded-md border border-gray-200 flex items-center justify-center">
                                <i class="fa-brands fa-cc-mastercard text-[#eb001b] text-lg"></i>
                            </span>
                            <span class="h-9 rounded-md border border-gray-200 flex items-center justify-center">
                                <i class="fa-brands fa-cc-paypal text-[#003087] text-lg"></i>
                            </span>
                            <span class="h-9 rounded-md border border-gray-200 flex items-center justify-center">
                                <i class="fa-brands fa-cc-stripe text-[#635bff] text-lg"></i>
                            </span>
                            <span class="h-9 rounded-md border border-gray-200 flex items-center justify-center gap-1 text-gray-800 text-xs font-medium">
                                <i class="fa-brands fa-apple"></i> Pay
                            </span>
                            <span class="h-9 rounded-md border border-gray-200 flex items-center justify-center text-xs font-medium text-gray-800">
                                <span class="text-blue-500">G</span> Pay
                            </span>
                        </div>
                    </div>

                    <p class="flex items-center justify-center gap-2 text-xs text-gray-400 mt-5">
                        <i class="fa-solid fa-shield-halved text-blue-500"></i>
                        Your payment information is secure and encrypted.
                    </p>
                </div>
            </div>
        </div>

        <!-- You May Also Like -->
        @endif
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">You May Also Like</h2>

            <div class="relative">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($related as $item)
                        <div class="flex flex-col">
                            @if(!empty($item['thumbnail']))
                                <img src="/{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="aspect-video rounded-xl object-cover">
                            @else
                                <div class="aspect-video rounded-xl overflow-hidden relative"
                                    style="background: linear-gradient(135deg, {{ $item['from'] }}, {{ $item['to'] }});">
                                    @if(!empty($item['label']))
                                        <div class="absolute inset-0 flex items-center justify-center p-4 text-center">
                                            <span class="text-white font-extrabold uppercase text-sm leading-tight"
                                                style="text-shadow: 0 2px 6px rgba(0,0,0,0.6);">
                                                {{ $item['label'] }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="absolute bottom-0 left-0 right-0 h-8 flex">
                                        @for($i = 0; $i < 22; $i++)
                                            <div class="flex-1 border-r border-black/30 bg-white/90"></div>
                                        @endfor
                                    </div>
                                </div>
                            @endif

                            <h3 class="mt-4 text-gray-900 font-semibold text-[15px]">{{ $item['name'] }}</h3>
                            <p class="text-xs text-gray-500">{{ $item['type'] }}</p>
                            <p class="mt-1 text-gray-900 font-bold">${{ number_format($item['price'], 2) }}</p>

                            <button type="button" data-add-slug="{{ $item['slug'] }}" data-reload="1" class="add-to-cart-btn mt-3 flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold py-2.5 rounded-lg transition">
                                <i class="fa-solid fa-cart-shopping text-xs"></i>
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
