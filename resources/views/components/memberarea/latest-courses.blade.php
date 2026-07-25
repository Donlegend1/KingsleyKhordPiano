<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-6">Latest Lessons</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($latestCourses as $category => $latestCourse)
            @php
                $lessonType = match ($category) {
                    'piano exercise' => 'upload',
                    'learn songs' => 'learn_song',
                    'extra courses' => 'extra_course',
                    default => null,
                };

                $lessonUrl = $category === 'guided practice'
                    ? "/member/piano-exercise/player?series=" . urlencode($latestCourse->series) . "&video_id={$latestCourse->id}"
                    : "/member/lesson/{$latestCourse->id}?type={$lessonType}";
            @endphp
            <div class="flex flex-col group">
                {{-- Thumbnail --}}
                <a href="{{ $lessonUrl }}" class="block relative aspect-video rounded-lg overflow-hidden bg-gray-50 border border-gray-100">
                    @if ($latestCourse->thumbnail_url)
                        <img src="{{ $latestCourse->thumbnail_url }}"
                             alt="{{ $latestCourse->title }}"
                             class="w-full h-full object-cover transition duration-300 group-hover:opacity-90">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-play text-gray-300 text-xl"></i>
                        </div>
                    @endif
                </a>

                {{-- Content --}}
                <div class="flex flex-col flex-1 pt-3">
                    <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wide mb-1.5">
                        {{ ucwords($category) }}
                    </span>

                    <h3 class="text-sm font-semibold text-gray-900 leading-snug line-clamp-2 mb-3">
                        <a href="{{ $lessonUrl }}" class="hover:text-gray-600 transition-colors">
                            {{ $latestCourse->title }}
                        </a>
                    </h3>

                    <a href="{{ $lessonUrl }}"
                       class="mt-auto inline-flex items-center justify-center gap-1.5 w-full px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-900 hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-colors duration-200">
                        Watch Now
                        <i class="fas fa-arrow-right text-xs transition-transform duration-200 group-hover:translate-x-0.5"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
