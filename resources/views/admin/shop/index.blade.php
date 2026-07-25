@extends('layouts.admin')

@section('content')
<main class="flex-1 p-6">
    <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Shop</h2>
            <p class="text-sm text-gray-500">Manage MIDI files and plugins sold in the shop.</p>
        </div>

        <a href="{{ route('admin.shop.create', ['type' => $type]) }}"
            class="inline-flex items-center gap-2 bg-gray-900 hover:bg-black text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
            <i class="fa-solid fa-plus text-xs"></i>
            Add {{ $type === 'plugin' ? 'Plugin' : 'MIDI File' }}
        </a>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="inline-flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm mb-6">
        <a href="{{ route('admin.shop.index', ['type' => 'midi']) }}"
            class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $type === 'midi' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            Midi File
        </a>
        <a href="{{ route('admin.shop.index', ['type' => 'plugin']) }}"
            class="px-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $type === 'plugin' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
            Plugins
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                    <th class="px-6 py-3">Thumbnail</th>
                    <th class="px-6 py-3">Title</th>
                    <th class="px-6 py-3">Regular Price</th>
                    <th class="px-6 py-3">Sale Price</th>
                    <th class="px-6 py-3">Download Link</th>
                    <th class="px-6 py-3">Video</th>
                    @if($type === 'plugin')
                        <th class="px-6 py-3">System Requirements</th>
                    @endif
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4">
                            @if($product->thumbnail)
                                <img src="/{{ $product->thumbnail }}" alt="{{ $product->title }}" class="w-14 h-14 rounded-lg object-cover">
                            @else
                                <div class="w-14 h-14 rounded-lg" style="background: linear-gradient(135deg, {{ $product->gradient_from }}, {{ $product->gradient_to }});"></div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $product->title }}</td>
                        <td class="px-6 py-4 text-gray-600">${{ number_format($product->regular_price, 2) }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->sale_price ? '$' . number_format($product->sale_price, 2) : '—' }}</td>
                        <td class="px-6 py-4">
                            @if($product->download_url)
                                <a href="{{ $product->download_url }}" target="_blank" class="text-blue-600 hover:underline text-xs">Link</a>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($product->video_url)
                                <a href="{{ $product->video_url }}" target="_blank" class="text-blue-600 hover:underline text-xs">Link</a>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        @if($type === 'plugin')
                            <td class="px-6 py-4 text-gray-500 text-xs max-w-[220px] truncate">{{ $product->system_requirements ?: '—' }}</td>
                        @endif
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.shop.edit', $product) }}" class="text-gray-500 hover:text-gray-900 transition" aria-label="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('admin.shop.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition" aria-label="Delete">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $type === 'plugin' ? 7 : 6 }}" class="px-6 py-10 text-center text-gray-400">
                            No {{ $type === 'plugin' ? 'plugins' : 'MIDI files' }} yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>
@endsection
