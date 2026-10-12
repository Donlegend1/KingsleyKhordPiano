@if (!empty($latestLessons))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center gap-2 mb-6">
        <i class="fas fa-tv text-[#1447A6]"></i>
        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Latest Lessons</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($latestLessons as $category => $lesson)
            <a href="/register" class="group block">
                {{-- Thumbnail with overlay --}}
                <div class="relative aspect-video rounded-xl overflow-hidden bg-gray-900">
                    @if ($lesson->thumbnail_url)
                        <img src="{{ $lesson->thumbnail_url }}"
                             alt="{{ $lesson->title }}"
                             class="w-full h-full object-cover opacity-80 transition duration-300 group-hover:scale-105 group-hover:opacity-90">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/10 to-transparent"></div>

                    <h3 class="absolute bottom-3 left-3 right-3 text-white font-bold text-base leading-snug line-clamp-2">
                        {{ $lesson->title }}
                    </h3>
                </div>

                {{-- Content --}}
                <div class="pt-3">
                    <span class="text-xs text-gray-500 font-medium">{{ ucwords($category) }}</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif
