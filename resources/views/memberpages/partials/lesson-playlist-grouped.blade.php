{{-- Lessons in this focus area, grouped by skill level, with prev/next controls --}}
@php
    // $groupedPlaylist keys are the canonical Basic/Competent/Challenging
    // labels; skill_level on the model itself may be cased differently, so
    // find the matching group key rather than trusting the raw value.
    $activeGroupLabel = null;
    if ($activeVideo) {
        foreach ($groupedPlaylist as $groupLevel => $items) {
            if ($items->contains(fn($item) => $item->id == $activeVideo->id)) {
                $activeGroupLabel = $groupLevel;
                break;
            }
        }
    }
@endphp
<div class="w-full border border-gray-100 rounded-xl overflow-hidden shadow-sm bg-white"
     x-data="{
        openGroups: {{ \Illuminate\Support\Js::from($activeGroupLabel ? [$activeGroupLabel] : []) }},
        toggleGroup(group) {
            this.openGroups.includes(group)
                ? this.openGroups = this.openGroups.filter(g => g !== group)
                : this.openGroups.push(group);
        },
     }">

    {{-- Header --}}
    <div class="px-5 py-4 border-b border-gray-100 bg-red-50">
        <p class="text-[11px] font-bold text-red-500 tracking-[0.14em] uppercase">
            Browse by Skill Level
        </p>
    </div>

    {{-- Grouped list --}}
    <div>
        @foreach ($groupedPlaylist as $groupLevel => $items)
            <div>
                <button type="button"
                    @click="toggleGroup({{ \Illuminate\Support\Js::from($groupLevel) }})"
                    class="w-full flex items-center justify-between px-5 py-3 bg-gray-700 hover:bg-gray-600 transition-colors">
                    <span class="text-[11px] font-semibold text-white tracking-[0.08em] uppercase">{{ $groupLevel }}</span>
                    <span class="text-white text-sm font-light leading-none flex-shrink-0 w-3.5 text-center"
                          x-text="openGroups.includes({{ \Illuminate\Support\Js::from($groupLevel) }}) ? '−' : '+'"></span>
                </button>

                <div x-show="openGroups.includes({{ \Illuminate\Support\Js::from($groupLevel) }})" x-transition x-cloak class="divide-y divide-gray-50">
                    @foreach ($items as $item)
                        @php
                            $isActive = $activeVideo && $item->id == $activeVideo->id;
                            $itemIsNew = $item->created_at
                                && $item->created_at->gt(now()->subDays(7))
                                && !\App\Models\LessonView::hasViewed(auth()->id(), $item);
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['video_id' => $item->id]) }}"
                            class="flex items-center gap-3 px-5 py-3.5 transition-colors
                    {{ $isActive ? 'bg-blue-600' : 'bg-white hover:bg-gray-50' }}">

                            <i class="fa-solid fa-play text-[9px] flex-shrink-0 {{ $isActive ? 'text-white' : 'text-gray-300' }}"></i>

                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] leading-snug flex items-center gap-2
                         {{ $isActive ? 'font-semibold text-white' : 'font-medium text-gray-700' }}">
                                    <span class="line-clamp-1">{{ $item->title }}</span>
                                    @if ($itemIsNew)
                                        <span class="bg-red-600 text-white text-[9px] font-semibold px-1.5 py-0.5 rounded flex-shrink-0">NEW</span>
                                    @endif
                                </p>
                            </div>
                            @if (in_array($item->id, $completedIds ?? []))
                                <i class="fa-solid fa-circle-check text-green-500 text-xs flex-shrink-0"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @include('memberpages.partials.sidebar-downloads', ['lesson' => $activeVideo])

    {{-- Prev / Next Lesson buttons (walks the combined list across all skill levels) --}}
    <div class="p-3 bg-white border-t border-gray-100 flex gap-2">
        @if ($previousVideo)
            <a href="{{ request()->fullUrlWithQuery(['video_id' => $previousVideo->id]) }}"
                class="flex items-center justify-center gap-2 w-1/2 py-2.5 rounded-lg text-gray-600 font-medium text-[13px] hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs"></i> Prev
            </a>
        @endif

        @if ($nextVideo)
            <a href="{{ request()->fullUrlWithQuery(['video_id' => $nextVideo->id]) }}"
                class="flex items-center justify-center gap-2 {{ $previousVideo ? 'w-1/2' : 'w-full' }} py-2.5 rounded-lg text-gray-600 font-medium text-[13px] hover:bg-gray-50 transition-colors">
                Next <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        @endif
    </div>

</div>
