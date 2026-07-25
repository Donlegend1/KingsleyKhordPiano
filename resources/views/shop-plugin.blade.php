@extends('layouts.app')

@section('title', $plugin['name'] . ' - Shop')

@php
    $related = [
        ['name' => 'Worship Keys Plugin', 'price' => 49.99, 'from' => '#3b0f5c', 'to' => '#12021f'],
        ['name' => 'Vintage Keys Plugin', 'price' => 59.99, 'from' => '#0f3b3b', 'to' => '#02100f'],
        ['name' => 'Gospel Organ Plugin', 'price' => 39.99, 'from' => '#3b0f0f', 'to' => '#100202'],
        ['name' => 'Ambient Pads Plugin', 'price' => 34.99, 'from' => '#241a3b', 'to' => '#05050a'],
    ];
@endphp

@section('content')
<div class="bg-white min-h-screen pt-32 pb-24 px-4">
    <div class="max-w-7xl mx-auto">


        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

            <!-- Left: Preview -->
            <div class="lg:col-span-3">
                <div class="aspect-video rounded-2xl overflow-hidden relative shadow-md bg-black">
                    @if($plugin['video_url'])
                        <iframe
                            class="absolute inset-0 w-full h-full"
                            src="{{ \App\Helpers\VideoHelper::linkToEmbed($plugin['video_url']) }}"
                            title="{{ $plugin['name'] }} preview"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen>
                        </iframe>
                    @elseif($plugin['thumbnail'])
                        <img src="/{{ $plugin['thumbnail'] }}" alt="{{ $plugin['name'] }}" class="absolute inset-0 w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center p-6"
                            style="background: linear-gradient(135deg, {{ $plugin['from'] }}, {{ $plugin['to'] }});">
                            <span class="text-white font-extrabold uppercase text-2xl text-center" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6);">
                                {{ $plugin['name'] }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Details -->
            <div class="lg:col-span-2">
                <span class="inline-block text-xs font-bold tracking-widest text-purple-700 bg-purple-50 px-3 py-1.5 rounded-md">
                    PLUGIN
                </span>

                <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">
                    {{ $plugin['name'] }}
                </h1>

                <div class="mt-6 text-4xl font-extrabold text-green-600">
                    ${{ number_format($plugin['price'], 2) }}
                </div>

                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <button type="button" data-add-slug="{{ $plugin['slug'] }}" class="add-to-cart-btn flex-1 flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold py-3.5 rounded-lg transition">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Add to Cart</span>
                    </button>
                    <button type="button" data-add-slug="{{ $plugin['slug'] }}" data-redirect="/checkout" class="add-to-cart-btn flex-1 bg-[#FFD736] hover:bg-[#e6c22e] text-black text-sm font-bold py-3.5 rounded-lg transition">
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

        <!-- System Requirements -->
        @if(!empty($plugin['system_requirements']))
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">System Requirements</h2>

                <div class="border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
                    @foreach(preg_split('/\r\n|\r|\n/', trim($plugin['system_requirements'])) as $line)
                        @continue(trim($line) === '')
                        @php
                            [$label, $value] = str_contains($line, ':')
                                ? array_map('trim', explode(':', $line, 2))
                                : [null, trim($line)];
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 px-6 py-4">
                            @if($label)
                                <span class="w-48 flex-shrink-0 font-semibold text-gray-900 text-sm">{{ $label }}</span>
                            @endif
                            <span class="text-gray-600 text-sm">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Related Plugins -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Plugins</h2>
            <div class="border-t border-gray-100 mb-8"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($related as $item)
                    <div class="flex flex-col">
                        <div class="aspect-video rounded-xl overflow-hidden relative"
                            style="background: linear-gradient(135deg, {{ $item['from'] }}, {{ $item['to'] }});">
                            <div class="absolute top-3 left-3 text-white font-bold text-sm"
                                style="text-shadow: 0 2px 6px rgba(0,0,0,0.6);">
                                {{ $item['name'] }}
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 h-8 flex">
                                @for($i = 0; $i < 22; $i++)
                                    <div class="flex-1 border-r border-black/30 bg-white/90"></div>
                                @endfor
                            </div>
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
