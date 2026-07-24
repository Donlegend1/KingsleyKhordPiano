@php
    $isEdit = isset($product);
    $type = $isEdit ? $product->type : $type;
@endphp

<div class="bg-white border border-gray-200 rounded-xl p-6 max-w-2xl">
    <form action="{{ $isEdit ? route('admin.shop.update', $product) : route('admin.shop.store') }}"
        method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <input type="hidden" name="type" value="{{ $type }}">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Thumbnail</label>
            @if($isEdit && $product->thumbnail)
                <img src="/{{ $product->thumbnail }}" alt="{{ $product->title }}" class="w-20 h-20 rounded-lg object-cover mb-2">
            @endif
            <input type="file" name="thumbnail" accept="image/*"
                class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-100 file:text-gray-700 file:font-semibold hover:file:bg-gray-200">
            @error('thumbnail')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Title</label>
            <input type="text" name="title" value="{{ old('title', $isEdit ? $product->title : '') }}" required
                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
            @error('title')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Regular Price ($)</label>
                <input type="number" step="0.01" min="0" name="regular_price"
                    value="{{ old('regular_price', $isEdit ? $product->regular_price : '') }}" required
                    class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                @error('regular_price')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sale Price ($)</label>
                <input type="number" step="0.01" min="0" name="sale_price"
                    value="{{ old('sale_price', $isEdit ? $product->sale_price : '') }}"
                    placeholder="Leave blank for no discount"
                    class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
                @error('sale_price')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Downloadable File Link</label>
            <input type="url" name="download_url" value="{{ old('download_url', $isEdit ? $product->download_url : '') }}"
                placeholder="https://... (where the buyer's download redirects to)"
                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
            @error('download_url')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">YouTube Video Embed</label>
            <input type="url" name="video_url" value="{{ old('video_url', $isEdit ? $product->video_url : '') }}"
                placeholder="https://www.youtube.com/watch?v=..."
                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
            @error('video_url')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        @if($type === 'plugin')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">System Requirements</label>
                <textarea name="system_requirements" rows="4"
                    placeholder="e.g. Operating System: Windows 10 or higher / macOS 10.13 or higher&#10;Plugin Format: VST3, AU, AAX&#10;RAM: 4 GB or more recommended&#10;Size: 2.1 GB"
                    class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">{{ old('system_requirements', $isEdit ? $product->system_requirements : '') }}</textarea>
                @error('system_requirements')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-gray-900 hover:bg-black text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                {{ $isEdit ? 'Save Changes' : 'Create Product' }}
            </button>
            <a href="{{ route('admin.shop.index', ['type' => $type]) }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
