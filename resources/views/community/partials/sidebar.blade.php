@php
    $contributorName = $contributorName ?? static function ($member) {
        $name = trim(($member->display_name ?? '') ?: (($member->first_name ?? '') . ' ' . ($member->last_name ?? '')));
        if ($name !== '') {
            return $name;
        }
        return $member->user_name ? ltrim($member->user_name, '@') : 'Community member';
    };
@endphp
<div
    class="lg:col-span-1 space-y-5"
    x-data="{
        stickyTop: 0,
        calcSticky() {
            if (window.innerWidth < 1024) {
                this.stickyTop = null;
                return;
            }
            this.stickyTop = Math.min(24, window.innerHeight - this.$el.offsetHeight - 16);
        }
    }"
    x-init="
        calcSticky();
        window.addEventListener('resize', calcSticky);
        window.addEventListener('load', calcSticky);
        document.fonts && document.fonts.ready.then(calcSticky);
        new ResizeObserver(() => calcSticky()).observe($el);
    "
    :style="stickyTop !== null ? `position: sticky; top: ${stickyTop}px; align-self: start;` : ''"
>

    @unless($hideCommunityStats ?? false)
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-sm shadow-md overflow-hidden">
        <h3 class="text-[15px] font-bold text-white px-5 py-3" style="background-color: #C85A5A;">Community Stats</h3>
        <div class="space-y-1 p-5">
            <div class="flex items-center gap-3 py-1.5">
                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-500 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400 flex-1">Members</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($memberCount) }}</span>
            </div>
            <div class="flex items-center gap-3 py-1.5">
                <span class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-500/10 text-green-500 dark:text-green-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="6" />
                    </svg>
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400 flex-1">Online</span>
                <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ number_format($onlineCount) }}</span>
            </div>
            <div class="flex items-center gap-3 py-1.5">
                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-500 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="1.75" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400 flex-1">Posts</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($postCount) }}</span>
            </div>
        </div>
    </div>
    @endunless

    {{-- Upcoming Events --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-sm shadow-md overflow-hidden">
        <h3 class="text-[15px] font-bold text-white px-5 py-3" style="background-color: #C85A5A;">Upcoming Events</h3>
        @if($upcomingEvents->isEmpty())
            <div class="px-5 pb-5">
                <p class="text-sm text-gray-400">No upcoming events scheduled yet.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach($upcomingEvents as $event)
                    @php
                        $eventUrl = $event->category === 'event'
                            ? '/member/live-session'
                            : route('member.live-session.confirm', $event);
                    @endphp
                    <a href="{{ $eventUrl }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <div class="text-center flex-shrink-0 rounded-lg overflow-hidden w-11 shadow-sm">
                            <div class="bg-blue-600 text-white text-[10px] font-bold uppercase py-0.5">
                                {{ $event->start_time->format('M') }}
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 text-sm font-bold text-gray-800 dark:text-gray-100 py-0.5">
                                {{ $event->start_time->format('d') }}
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-tight truncate">{{ $event->title }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $event->start_time->format('M d, Y g:i A') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Popular Contributors --}}
    @php
        $rankStyles = [
            0 => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
            1 => 'bg-slate-100 text-slate-600 dark:bg-gray-700 dark:text-slate-300',
            2 => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300',
        ];
    @endphp
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-sm shadow-md overflow-hidden" x-data="{ range: 'month' }">
        <div class="flex items-center justify-between px-5 py-3" style="background-color: #C85A5A;">
            <h3 class="text-[15px] font-bold text-white">Popular Contributors</h3>
            <div class="flex items-center gap-3">
                @foreach(['month' => 'Month', 'all' => 'All Time'] as $key => $label)
                    <button
                        type="button"
                        @click="range = '{{ $key }}'"
                        :class="range === '{{ $key }}' ? 'text-white' : 'text-white/60 hover:text-white/90'"
                        class="text-xs font-semibold transition-colors"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        @foreach($contributors as $key => $rows)
            <div x-show="range === '{{ $key }}'" x-cloak class="px-5 pt-3 pb-1">
                @if($rows->isEmpty())
                    <p class="text-sm text-gray-400 py-3">No activity yet.</p>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach($rows as $i => $member)
                            <div class="flex items-center gap-3 py-2.5">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 {{ $rankStyles[$i] ?? 'text-gray-300 dark:text-gray-600' }}">
                                    {{ $i + 1 }}
                                </span>
                                @if($member->passport)
                                    <img src="{{ asset($member->passport) }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 ring-2 ring-white dark:ring-gray-800 shadow-sm" alt="">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 dark:from-gray-600 dark:to-gray-700 text-white flex items-center justify-center text-xs font-bold flex-shrink-0 ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                        {{ strtoupper(substr($contributorName($member), 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm text-gray-800 dark:text-gray-100 font-semibold truncate">{{ $contributorName($member) }}</p>
                                    <span class="text-xs text-green-600 dark:text-green-400 font-semibold">+{{ number_format($member->total_points) }} pts</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach

        <a href="{{ route('community.leaderboard') }}" class="block text-center text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 border-t border-gray-50 dark:border-gray-700 py-3 transition-colors">
            Show More
        </a>
    </div>

    {{-- Latest Activity --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-sm shadow-md overflow-hidden">
        <h3 class="text-[15px] font-bold text-white px-5 py-3" style="background-color: #C85A5A;">Latest Activity</h3>

        <div class="divide-y divide-gray-50 dark:divide-gray-700 p-5">
            @php
                $typeStyles = [
                    'Post' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
                    'Comment' => 'bg-teal-50 text-teal-600 dark:bg-teal-500/10 dark:text-teal-400',
                    'Reply' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
                ];
            @endphp
            @forelse($latestActivity as $item)
                <a href="{{ $item['url'] }}" class="block py-3 -mx-1 px-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-md {{ $typeStyles[$item['type']] ?? 'bg-gray-100 text-gray-500' }}">
                            {{ $item['type'] }}
                        </span>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">{{ $item['excerpt'] }}</p>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $contributorName($item['user']) }} &middot; {{ $item['created_at']->diffForHumans() }}</p>
                </a>
            @empty
                <p class="text-sm text-gray-400 py-2">No activity yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Members --}}
    @unless($hideRecentMembers ?? false)
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-sm shadow-md overflow-hidden">
        <h3 class="text-[15px] font-bold text-white px-5 py-3" style="background-color: #C85A5A;">Recent Members</h3>
        @if($recentMembers->isEmpty())
            <p class="text-sm text-gray-400 p-4">No members yet.</p>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($recentMembers as $member)
                    @php $isNew = $member->created_at->gt(now()->subDay()); @endphp
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="relative flex-shrink-0">
                            @if($member->passport)
                                <img
                                    src="{{ asset($member->passport) }}"
                                    class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm"
                                    alt=""
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 dark:from-gray-600 dark:to-gray-700 text-white items-center justify-center text-xs font-bold ring-2 ring-white dark:ring-gray-800 shadow-sm" style="display:none">
                                    {{ strtoupper(substr($member->display_name ?: $member->first_name, 0, 1)) }}
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 dark:from-gray-600 dark:to-gray-700 text-white flex items-center justify-center text-xs font-bold ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                    {{ strtoupper(substr($member->display_name ?: $member->first_name, 0, 1)) }}
                                </div>
                            @endif
                            @if($isNew)
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-green-500 ring-2 ring-white dark:ring-gray-800"></span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">
                                {{ $member->display_name ?: trim($member->first_name . ' ' . $member->last_name) }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $isNew ? 'Joined just now' : 'Joined ' . $member->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    @endunless

</div>
