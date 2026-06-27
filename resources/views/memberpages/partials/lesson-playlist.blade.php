{{-- Lessons in this course playlist + prev/next controls --}}
<div class="w-full border border-gray-100 rounded-xl overflow-hidden shadow-sm bg-white">

    {{-- Header --}}
    <div class="px-5 py-4 border-b border-gray-100">
        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase">
            Lessons in this course:
        </p>
    </div>

    {{-- Scrollable list --}}
    <div class="overflow-y-auto" style="max-height: 520px;">
        @foreach ($playlist as $item)
            @php $isActive = $activeVideo && $item->id == $activeVideo->id; @endphp
            <a href="{{ request()->fullUrlWithQuery(['video_id' => $item->id]) }}"
                class="flex items-center gap-3 px-5 py-4 border-b border-gray-50 transition-colors
        {{ $isActive ? 'bg-blue-50' : 'bg-white hover:bg-gray-50' }}">

                {{-- Title --}}
                <div class="flex-1 min-w-0">
                    <p
                        class="text-[12px] font-bold uppercase tracking-wide leading-snug
             {{ $isActive ? 'text-blue-700' : 'text-gray-800' }}">
                        {{ $item->title }}
                    </p>
                </div>
                @if (in_array($item->id, $completedIds ?? []))
                    <i class="fa-solid fa-circle-check text-green-500 text-sm flex-shrink-0"></i>
                @endif
            </a>
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
        @else
            <button disabled
                class="w-full py-3 border border-gray-100 rounded-lg text-gray-400 font-bold text-[14px] bg-gray-50 cursor-not-allowed">
                Course Completed
            </button>
        @endif
    </div>

</div>
