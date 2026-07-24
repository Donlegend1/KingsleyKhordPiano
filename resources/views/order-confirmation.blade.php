@extends('layouts.app')

@section('title', 'Order Confirmation - Shop')

@section('content')
<div class="bg-gray-50 min-h-screen pt-32 pb-24 px-4">
    <div class="max-w-4xl mx-auto">


        <!-- Success -->
        <div class="text-center mb-10">
            <div class="relative inline-block">
                <span class="absolute -top-2 -left-10 w-1.5 h-1.5 rounded-full bg-[#FFD736]"></span>
                <span class="absolute top-4 -left-16 w-2 h-4 rounded-full bg-green-400 rotate-12"></span>
                <span class="absolute -top-1 -right-10 w-1.5 h-1.5 rounded-full bg-[#FFD736]"></span>
                <span class="absolute top-6 -right-16 w-2 h-2 rounded-full bg-green-400"></span>
                <span class="absolute top-14 -right-20 w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                <span class="absolute top-16 -left-20 w-2 h-2 rotate-45 bg-green-400"></span>

                <div class="w-20 h-20 rounded-full border-4 border-green-500 flex items-center justify-center">
                    <i class="fa-solid fa-check text-green-500 text-3xl"></i>
                </div>
            </div>

            <h1 class="mt-6 text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Thank You for Your Purchase!</h1>
            <p class="mt-2 text-gray-500">Your order has been received and is now complete.</p>
        </div>

        <!-- Order Meta -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-file-lines text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Order Number</p>
                        <p class="font-bold text-gray-900 text-sm">{{ $orderNumber }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-calendar-days text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Date</p>
                        <p class="font-bold text-gray-900 text-sm">{{ $orderDate }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-sack-dollar text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Total Paid</p>
                        <p class="font-bold text-gray-900 text-sm">${{ number_format($total, 2) }} USD</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Successful Banner -->
        <div class="flex items-start gap-3 bg-green-50 border border-green-100 rounded-xl px-6 py-4 mb-6">
            <i class="fa-solid fa-shield-halved text-green-600 mt-0.5"></i>
            <div>
                <p class="font-semibold text-green-800 text-sm">Payment Successful</p>
                <p class="text-green-700 text-sm mt-0.5">A confirmation email has been sent to {{ $email }}</p>
            </div>
        </div>

        <!-- Downloads -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900">Your Downloads</h2>
            <p class="text-sm text-gray-500 mb-4">You can download your purchased items below.</p>

            <div class="divide-y divide-gray-100">
                @foreach($items as $item)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 py-5">
                        @if(!empty($item['thumbnail']))
                            <img src="/{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-16 h-16 rounded-lg overflow-hidden relative flex-shrink-0"
                                style="background: linear-gradient(135deg, {{ $item['from'] }}, {{ $item['to'] }});">
                                <div class="absolute inset-0 flex items-center justify-center p-1 text-center">
                                    <span class="text-white font-extrabold uppercase text-[8px] leading-tight" style="text-shadow: 0 1px 4px rgba(0,0,0,0.6);">
                                        {{ $item['name'] }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-[15px]">{{ $item['name'] }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item['meta'] }}</p>
                        </div>

                        <div class="text-left sm:text-right flex-shrink-0">
                            <p class="font-bold text-gray-900">${{ number_format($item['price'], 2) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Qty: {{ $item['qty'] }}</p>
                        </div>

                        <div class="flex flex-col items-start sm:items-end gap-1.5 flex-shrink-0">
                            @if($item['downloadUrl'])
                                <a href="{{ $item['downloadUrl'] }}" target="_blank" class="flex items-center gap-2 border border-gray-200 hover:border-gray-300 text-gray-800 text-sm font-semibold px-4 py-2 rounded-lg transition">
                                    <i class="fa-solid fa-download text-xs"></i>
                                    {{ $item['downloadLabel'] }}
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">Download link coming soon</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        <!-- Need Help -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-gray-200 rounded-xl p-6 mb-10">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-headset text-purple-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Need Help?</p>
                    <p class="text-sm text-gray-500 mt-0.5">If you have any questions or issues with your downloads, our support team is here to help.</p>
                </div>
            </div>
            <a href="/contact" class="flex items-center justify-center gap-2 border border-gray-200 hover:border-gray-300 text-gray-800 text-sm font-semibold px-5 py-2.5 rounded-lg transition whitespace-nowrap">
                <i class="fa-regular fa-envelope text-xs"></i>
                Contact Support
            </a>
        </div>

        <!-- Continue Shopping -->
        <div class="text-center">
            <a href="/shop" class="inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white font-semibold px-8 py-3.5 rounded-lg transition">
                Continue Shopping
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </a>
        </div>

    </div>
</div>
@endsection
