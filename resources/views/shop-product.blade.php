@extends('layouts.app')

@section('title', $product['name'] . ' - Shop')

@php
    $related = [
        ['name' => 'Thank You Lord MIDI', 'price' => 12.99, 'label' => 'THANK YOU LORD', 'sub' => 'LORD', 'from' => '#3b2f1e', 'to' => '#0d0d0d'],
        ['name' => 'Great Is Thy Faithfulness MIDI', 'price' => 13.99, 'label' => 'GREAT IS THY FAITHFULNESS', 'sub' => 'MIDI', 'from' => '#5b21b6', 'to' => '#1e0a3c'],
        ['name' => 'You Deserve The Glory MIDI', 'price' => 13.99, 'label' => 'YOU DESERVE THE GLORY', 'sub' => 'MIDI', 'from' => '#0f2942', 'to' => '#04101c'],
        ['name' => 'What A Beautiful Name MIDI', 'price' => 13.99, 'label' => 'WHAT A BEAUTIFUL NAME', 'sub' => 'MIDI', 'from' => '#241a3b', 'to' => '#05050a'],
    ];
@endphp

@section('content')
<div class="bg-white min-h-screen pt-32 pb-24 px-4">
    <div class="max-w-7xl mx-auto">


        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

            <!-- Left: Preview -->
            <div class="lg:col-span-3">
                <div class="aspect-video rounded-2xl overflow-hidden relative shadow-md bg-black">
                    @if($product['video_url'])
                        <iframe
                            class="absolute inset-0 w-full h-full"
                            src="{{ \App\Helpers\VideoHelper::linkToEmbed($product['video_url']) }}"
                            title="{{ $product['name'] }} preview"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    @elseif($product['thumbnail'])
                        <img src="/{{ $product['thumbnail'] }}" alt="{{ $product['name'] }}" class="absolute inset-0 w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center p-6"
                            style="background: linear-gradient(135deg, {{ $product['from'] }}, {{ $product['to'] }});">
                            <span class="text-white font-extrabold uppercase text-2xl text-center" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6);">
                                {{ $product['name'] }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Details -->
            <div class="lg:col-span-2">
                <span class="inline-block text-xs font-bold tracking-widest text-blue-700 bg-blue-50 px-3 py-1.5 rounded-md">
                    MIDI FILE
                </span>

                <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ $product['name'] }}
                </h1>

                <div class="mt-6 text-4xl font-extrabold text-green-600">
                    ${{ number_format($product['price'], 2) }}
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <button type="button" data-add-slug="{{ $product['slug'] }}" class="add-to-cart-btn flex-1 flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold py-3.5 rounded-lg transition">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Add to Cart</span>
                    </button>
                    <button type="button" data-add-slug="{{ $product['slug'] }}" data-redirect="/checkout" class="add-to-cart-btn flex-1 bg-[#FFD736] hover:bg-[#e6c22e] text-black text-sm font-bold py-3.5 rounded-lg transition">
                        Buy Now
                    </button>
                </div>

                <!-- Secure checkout -->
                <div class="mt-8 text-center">
                    <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                        <div class="flex-1 border-t border-gray-100"></div>
                        Secure checkout with
                        <div class="flex-1 border-t border-gray-100"></div>
                    </div>
                    <div class="flex items-center justify-center flex-wrap gap-3">
                        <span class="w-16 h-9 rounded-md border border-gray-200 flex items-center justify-center">
                            <i class="fa-brands fa-cc-visa text-[#1a1f71] text-xl"></i>
                        </span>
                        <span class="w-16 h-9 rounded-md border border-gray-200 flex items-center justify-center">
                            <i class="fa-brands fa-cc-mastercard text-[#eb001b] text-xl"></i>
                        </span>
                        <span class="w-16 h-9 rounded-md border border-gray-200 flex items-center justify-center">
                            <i class="fa-brands fa-cc-paypal text-[#003087] text-xl"></i>
                        </span>
                        <span class="w-16 h-9 rounded-md border border-gray-200 flex items-center justify-center">
                            <i class="fa-brands fa-cc-stripe text-[#635bff] text-xl"></i>
                        </span>
                        <span class="px-3 h-9 rounded-md border border-gray-200 flex items-center justify-center gap-1.5 text-gray-800 text-sm font-medium">
                            <i class="fa-brands fa-apple text-base"></i> Pay
                        </span>
                        <span class="px-3 h-9 rounded-md border border-gray-200 flex items-center justify-center text-sm font-medium text-gray-800">
                            <span class="text-blue-500">G</span> Pay
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related MIDI Files -->
        <div class="mt-20">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related MIDI Files</h2>
            <div class="border-t border-gray-100 mb-8"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($related as $item)
                    <div class="flex flex-col">
                        <div class="aspect-video rounded-xl overflow-hidden relative flex items-end p-4"
                            style="background: linear-gradient(135deg, {{ $item['from'] }}, {{ $item['to'] }});">
                            <span class="text-white font-extrabold uppercase leading-tight text-lg"
                                style="text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
                                {{ $item['label'] }}<br>
                                <span class="text-[#FFD736]">{{ $item['sub'] }}</span>
                            </span>
                        </div>

                        <h3 class="mt-4 text-gray-900 font-semibold text-[15px]">{{ $item['name'] }}</h3>
                        <p class="mt-1 text-gray-900 font-bold">${{ number_format($item['price'], 2) }}</p>

                        <button class="mt-3 flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold py-2.5 rounded-lg transition">
                            <i class="fa-solid fa-cart-shopping text-xs"></i>
                            Add to Cart
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
