{{-- Lessons in this course playlist + prev/next controls --}}
<div class="w-full border border-gray-100 rounded-xl overflow-hidden shadow-sm bg-white">

    {{-- Header --}}
    <div class="px-5 py-4 border-b border-gray-100 bg-red-50">
        <p class="text-[11px] font-bold text-red-500 tracking-[0.14em] uppercase">
            Lessons in this course:
        </p>
    </div>

    {{-- Scrollable list --}}
    <div>
        @foreach ($playlist as $item)
            @php
                $isActive = $activeVideo && $item->id == $activeVideo->id;
                $itemIsNew = $item->created_at
                    && $item->created_at->gt(now()->subDays(7))
                    && !\App\Models\LessonView::hasViewed(auth()->id(), $item);
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['video_id' => $item->id]) }}"
                class="flex items-center gap-3 px-5 py-4 border-b border-gray-50 transition-colors
        {{ $isActive ? 'bg-red-600' : 'bg-gray-50 hover:bg-gray-100' }}">

                {{-- Title --}}
                <div class="flex-1 min-w-0">
                    <p
                        class="text-[12px] font-bold uppercase tracking-wide leading-snug flex items-center gap-2
             {{ $isActive ? 'text-white' : 'text-gray-800' }}">
                        <span class="truncate">{{ $item->title }}</span>
                        @if ($itemIsNew)
                            <span class="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md tracking-wide flex-shrink-0">NEW</span>
                        @endif
                    </p>
                </div>
                @if (in_array($item->id, $completedIds ?? []))
                    <i class="fa-solid fa-circle-check text-green-500 text-sm flex-shrink-0"></i>
                @endif
            </a>

            @if ($isActive && !empty($item->related_lessons))
                <div class="bg-white border-b border-gray-100 px-5 py-4">
                    <div class="flex items-center gap-1.5 mb-3">
                        <i class="fa-solid fa-link text-indigo-500 text-[10px]"></i>
                        <p class="text-[10px] font-bold text-gray-400 tracking-[0.14em] uppercase">
                            Related Lessons
                        </p>
                    </div>
                    <div class="space-y-2">
                        @foreach ($item->related_lessons as $related)
                            <a href="{{ $related['url'] }}"
                                class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white border border-gray-100 hover:border-indigo-200 hover:shadow-sm transition-all group">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-50 text-indigo-500 flex-shrink-0 group-hover:bg-indigo-100 transition-colors">
                                    <i class="fa-solid fa-play text-[8px] ml-0.5"></i>
                                </span>
                                <p class="text-[11px] font-semibold text-gray-700 group-hover:text-indigo-700 truncate flex-1 transition-colors">
                                    {{ $related['title'] }}
                                </p>
                                <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-indigo-400 text-[9px] flex-shrink-0 transition-colors"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Next Lesson button --}}
    <div class="p-4 bg-white border-t border-gray-100 flex gap-2">
        @if ($previousVideo)
            <a href="{{ request()->fullUrlWithQuery(['video_id' => $previousVideo->id]) }}"
                class="flex items-center justify-center gap-2 w-1/2 py-3 border border-gray-200 rounded-lg text-gray-800 font-bold text-[14px] hover:bg-blue-50 hover:border-blue-200 transition-all">
                <i class="fa-solid fa-arrow-left text-sm"></i> Prev
            </a>
        @endif

        @if ($nextVideo)
            <a href="{{ request()->fullUrlWithQuery(['video_id' => $nextVideo->id]) }}"
                class="flex items-center justify-center gap-2 {{ $previousVideo ? 'w-1/2' : 'w-full' }} py-3 border border-gray-200 rounded-lg text-gray-800 font-bold text-[14px] hover:bg-blue-50 hover:border-blue-200 transition-all">
                Next <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
        @endif
    </div>

</div>
