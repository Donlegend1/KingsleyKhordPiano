<div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm mt-4 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3" style="background-color: #C85A5A;">
        <h3 class="text-[11px] font-bold uppercase tracking-wide text-white">Topics</h3>
        <div class="flex items-center gap-2" x-data="{ sortOpen: false }">
            <form method="POST" action="{{ route('community.forum.mark-all-read', $category['subcategory']) }}">
                @csrf
                <button type="submit" class="flex items-center gap-1 bg-white text-gray-800 text-[11px] font-semibold px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-50 transition-colors">
                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Mark all as Read
                </button>
            </form>
            <div class="relative" @click.outside="sortOpen = false">
                <button type="button" @click="sortOpen = !sortOpen" class="flex items-center gap-1 bg-white text-gray-800 text-[11px] font-semibold px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-50 transition-colors">
                    Sort By
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
                <div x-show="sortOpen" x-cloak class="absolute right-0 mt-1 w-40 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-lg z-10 overflow-hidden">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" class="block px-3 py-2 text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60">Latest</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}" class="block px-3 py-2 text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60">Oldest</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" class="block px-3 py-2 text-xs text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60">Most Viewed</a>
                </div>
            </div>
        </div>
    </div>
    @forelse($topics as $topic)
        <a href="{{ $topic['url'] }}"
            class="flex items-start sm:items-center gap-3 sm:gap-4 px-4 sm:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors {{ !$loop->first ? 'border-t border-gray-100 dark:border-gray-700' : '' }}">
            @if($topic['is_read'])
                <span class="w-2 h-2 mt-1.5 sm:mt-0 flex-shrink-0"></span>
            @else
                <span class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 sm:mt-0 flex-shrink-0"></span>
            @endif

            <div class="flex-1 min-w-0">
                <p class="text-sm sm:text-base {{ $topic['is_read'] ? 'font-normal text-gray-900 dark:text-white' : 'font-bold text-gray-900 dark:text-white' }} leading-snug sm:truncate">
                    @if($topic['is_pinned'])
                        <span title="Pinned">📌</span>
                    @endif
                    {{ $topic['title'] }}
                </p>
                <p class="text-xs text-gray-400 mt-1 sm:mt-0.5">By {{ $topic['author'] }}, {{ $topic['created_at']->format('F j, Y') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 sm:hidden">
                    {{ $topic['replies'] }} {{ Str::plural('reply', $topic['replies']) }} &nbsp;&nbsp; {{ $topic['views'] }} {{ Str::plural('view', $topic['views']) }}
                </p>
            </div>

            <div class="hidden sm:block text-right text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 w-20">
                <p>{{ $topic['views'] }} {{ Str::plural('view', $topic['views']) }}</p>
            </div>

            {{-- Mobile: avatar + date stacked --}}
            <div class="flex flex-col items-center gap-1 flex-shrink-0 sm:hidden">
                @if($topic['avatar'])
                    <img src="{{ asset($topic['avatar']) }}" alt="{{ $topic['author'] }}" class="w-9 h-9 rounded-full object-cover">
                @else
                    <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400">
                        {{ strtoupper(substr($topic['author'], 0, 1)) }}
                    </div>
                @endif
                <span class="text-[11px] text-gray-400">{{ $topic['created_at']->format('M j') }}</span>
            </div>

            {{-- Desktop: avatar + name + date --}}
            <div class="hidden sm:flex items-center gap-2.5 flex-shrink-0 w-40">
                @if($topic['avatar'])
                    <img src="{{ asset($topic['avatar']) }}" alt="{{ $topic['author'] }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                @else
                    <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                        {{ strtoupper(substr($topic['author'], 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ $topic['author'] }}</p>
                    <p class="text-xs text-gray-400">{{ $topic['created_at']->format('M j') }}</p>
                </div>
            </div>
        </a>
    @empty
        <div class="px-5 py-10 text-center text-sm text-gray-400">No topics posted here yet.</div>
    @endforelse

    @if($topics->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $topics->links('pagination.community') }}
        </div>
    @endif
</div>
