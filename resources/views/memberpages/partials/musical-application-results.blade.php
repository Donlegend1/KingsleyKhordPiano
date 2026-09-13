<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($applications as $seriesName => $items)
        @php
            $firstItem = $items->first();
            $lessonCount = count($items);
            $playerUrl = route('piano.exercise.player', ['series' => $seriesName, 'skill_level' => strtolower($firstItem->skill_level)]);
            $isNew = \App\Models\LessonView::anyNewUnviewed(auth()->id(), $items);
            $levelStyles = [
                'beginner'     => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                'intermediate' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                'advanced'     => 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400',
            ];
            $levelStyle = $levelStyles[strtolower($firstItem->skill_level)] ?? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400';
        @endphp
        <div
            class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col group">

            <!-- Thumbnail -->
            <a href="{{ $playerUrl }}" class="block relative overflow-hidden" style="aspect-ratio:16/9;">
                @if($firstItem->thumbnail_url ?? null)
                    <img src="{{ $firstItem->thumbnail_url }}" alt="{{ $seriesName }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-900 to-purple-900">
                        <i class="fa fa-music text-5xl text-white/30"></i>
                    </div>
                @endif

                @if($isNew)
                    <div class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-md tracking-wide">
                        NEW
                    </div>
                @endif

                <!-- Lesson count badge -->
                <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md">
                    {{ $lessonCount }} {{ Str::plural('Lesson', $lessonCount) }}
                </div>

                <!-- Play overlay -->
                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl scale-90 group-hover:scale-100 transition duration-300">
                        <i class="fa fa-play text-black text-sm ml-0.5"></i>
                    </div>
                </div>
            </a>

            <!-- Card Body -->
            <div class="p-5 flex flex-col gap-3 flex-1">
                <h3 class="text-[15px] font-bold text-gray-900 dark:text-white leading-snug">
                    {{ $seriesName }}
                </h3>

                <!-- Level Badge -->
                <div>
                    <span class="inline-flex items-center gap-1.5 {{ $levelStyle }} text-xs font-semibold px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ $firstItem->skill_level }}
                    </span>
                </div>

                <!-- Watch Now Button -->
                <a href="{{ $playerUrl }}"
                   class="mt-auto flex items-center justify-center w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all duration-200">
                    Watch Now
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100">
            <i class="fa-regular fa-folder-open text-gray-200 text-6xl block mb-4"></i>
            @if($search)
                <h3 class="text-xl font-bold text-gray-800">No drills found for "{{ $search }}"</h3>
            @else
                <h3 class="text-xl font-bold text-gray-800">No applications found</h3>
            @endif
            <p class="text-gray-400">Try selecting a different skill level or check back later.</p>
        </div>
    @endforelse
</div>

@if ($seriesPage->total() > 0)
    <p class="text-center text-xs text-gray-400 mt-6">
        Showing {{ $seriesPage->firstItem() }}&ndash;{{ $seriesPage->lastItem() }} of {{ $seriesPage->total() }} {{ Str::plural('result', $seriesPage->total()) }}
    </p>
@endif

@if ($seriesPage->hasPages())
    <div class="flex justify-center py-4">
        {{ $seriesPage->links('components.pagination') }}
    </div>
@endif
