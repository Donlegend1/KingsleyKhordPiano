@extends('layouts.app')

@section('title', 'Shop')

@section('content')
<div x-data="{ tab: 'shop', search: '' }" class="bg-gray-50 min-h-screen pt-32 pb-24 px-4">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="inline-block text-[11px] font-bold tracking-[0.2em] text-gray-500 uppercase mb-3">Sound Store</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight">Shop</h1>
            <div class="mt-4 mx-auto w-14 h-1 rounded-full bg-[#FFD736]"></div>
        </div>

        <!-- Tabs + Search -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
            <div class="inline-flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                <button @click="tab = 'shop'"
                    :class="tab === 'shop' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200">
                    Shop
                </button>
                <button @click="tab = 'midi'"
                    :class="tab === 'midi' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200">
                    Midi Files
                </button>
                <button @click="tab = 'plugin'"
                    :class="tab === 'plugin' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200">
                    Plugins
                </button>
            </div>

            <div class="flex items-center w-full sm:w-80 bg-white border border-gray-200 rounded-full pl-5 pr-1.5 py-1.5 shadow-sm transition-all duration-200 focus-within:border-transparent focus-within:ring-2 focus-within:ring-blue-600">
                <input type="text" x-model="search" placeholder="Search for products"
                    class="flex-1 min-w-0 bg-transparent text-sm text-gray-800 placeholder:text-gray-400 border-none focus:outline-none focus:ring-0">
                <button type="button" aria-label="Search"
                    class="flex-shrink-0 w-9 h-9 rounded-full bg-blue-600 hover:bg-blue-700 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-white text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                @php
                    $discount = $product['original_price']
                        ? round((1 - $product['price'] / $product['original_price']) * 100)
                        : null;
                @endphp
                <div x-show="(tab === 'shop' || tab === '{{ $product['type'] }}') && '{{ strtolower($product['name']) }}'.includes(search.toLowerCase())" x-transition
                    class="group flex flex-col bg-white rounded-2xl border border-gray-200 p-4 shadow-sm transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    @php
                        $href = $product['type'] === 'midi'
                            ? '/shop/midi-files/' . $product['slug']
                            : '/shop/plugins/' . $product['slug'];
                    @endphp
                    <a href="{{ $href }}" class="aspect-square rounded-xl overflow-hidden relative block">
                        @if(!empty($product['thumbnail']))
                            <img src="/{{ $product['thumbnail'] }}" alt="{{ $product['name'] }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="absolute inset-0 transition-transform duration-500 group-hover:scale-105"
                                style="background: linear-gradient(135deg, {{ $product['from'] }}, {{ $product['to'] }});">
                            </div>

                            <div class="absolute inset-0 flex items-center justify-center p-6">
                                <span class="text-white font-extrabold uppercase leading-tight text-xl text-center"
                                    style="text-shadow: 0 2px 10px rgba(0,0,0,0.55);">
                                    {{ $product['label'] }}
                                </span>
                            </div>
                        @endif

                        @if($discount)
                            <span class="absolute top-3 left-3 bg-[#FFD736] text-black text-xs font-bold px-2 py-1 rounded-md shadow-sm">
                                -{{ $discount }}%
                            </span>
                        @endif
                    </a>

                    <span class="mt-4 self-start text-[10px] font-bold tracking-widest text-gray-500 bg-gray-100 px-2 py-1 rounded-md">
                        {{ $product['type'] === 'midi' ? 'MIDI FILE' : 'PLUGINS' }}
                    </span>

                    <h3 class="mt-2 text-gray-900 font-semibold text-[15px] leading-snug">
                        @if($href)
                            <a href="{{ $href }}" class="hover:text-gray-600 transition">{{ $product['name'] }}</a>
                        @else
                            {{ $product['name'] }}
                        @endif
                    </h3>

                    <p class="mt-1.5 flex items-center gap-2">
                        <span class="text-gray-900 font-bold text-lg">${{ number_format($product['price'], 2) }}</span>
                        @if($product['original_price'])
                            <span class="text-gray-400 line-through text-sm">${{ number_format($product['original_price'], 2) }}</span>
                        @endif
                    </p>

                    <button type="button" data-add-slug="{{ $product['slug'] }}"
                        class="add-to-cart-btn mt-4 flex items-center justify-center gap-2 bg-gray-900 hover:bg-[#FFD736] text-white hover:text-black text-sm font-semibold py-2.5 rounded-lg transition-colors duration-200 disabled:opacity-60">
                        <i class="fa-solid fa-cart-shopping text-xs"></i>
                        <span>Add to cart</span>
                    </button>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
