@extends('layouts.hub')

@section('title', 'Leaderboard')

@section('breadcrumbs')
    @include('community.partials.breadcrumbs', ['items' => [['label' => 'Leaderboard']]])
@endsection

@section('content')
@php
    $displayName = static function ($member) {
        $name = trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? ''));
        if ($name !== '') {
            return $name;
        }
        return $member->user_name ? ltrim($member->user_name, '@') : 'Community member';
    };
@endphp
<div
    x-data="{ activeTab: 'leaderboard' }"
    class="min-h-full max-w-full overflow-x-hidden bg-gray-50 px-4 py-6 dark:bg-black sm:px-6 lg:px-8"
>
    <div class="mx-auto max-w-6xl w-full min-w-0 space-y-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400 dark:text-slate-500">Community Rankings</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-xl">
                    Leaderboard shows the highest point earners this month, Top Members ranks all-time totals.
                </p>
            </div>

            <div class="w-full rounded-xl bg-gray-100 p-1 dark:bg-[#161617] sm:w-auto">
                <div class="grid grid-cols-2 gap-1">
                    <button
                        type="button"
                        @click="activeTab = 'leaderboard'"
                        :class="activeTab === 'leaderboard' ? 'bg-white text-gray-900 shadow-sm dark:bg-[#1f2021] dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                    >
                        Leaderboard
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'top-members'"
                        :class="activeTab === 'top-members' ? 'bg-white text-gray-900 shadow-sm dark:bg-[#1f2021] dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white'"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                    >
                        Top Members
                    </button>
                </div>
            </div>
        </div>

        <div class="min-w-0">
            <section
                x-show="activeTab === 'leaderboard'"
                x-transition.opacity.duration.200ms
                x-cloak
            >
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Leaderboard</h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Highest point totals for {{ now()->format('F Y') }}.</p>
                </div>

                @if ($monthlyLeaders->currentPage() === 1 && $monthlyLeaders->count() > 0)
                    <div class="mt-4">
                        @include('community.partials.leaderboard-podium', [
                            'members' => $monthlyLeaders->take(3),
                            'displayName' => $displayName,
                            'stats' => [
                                ['key' => 'total_points', 'label' => 'Points'],
                                ['key' => 'posts_count', 'label' => 'Posts'],
                            ],
                        ])
                    </div>
                @endif

                <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#161617] {{ $monthlyLeaders->currentPage() === 1 && $monthlyLeaders->total() <= 3 ? 'hidden' : '' }}">
                    @php $skipTop3 = $monthlyLeaders->currentPage() === 1; @endphp
                    @forelse ($monthlyLeaders as $index => $member)
                        @php $rank = $monthlyLeaders->firstItem() + $index - 1; @endphp
                        @continue($skipTop3 && $rank < 3)
                        <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-5 transition-colors hover:bg-gray-50 dark:hover:bg-white/[0.03] {{ $loop->last ? '' : 'border-b border-gray-100 dark:border-white/10' }}">
                            <div class="flex min-w-0 flex-1 items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold bg-gray-100 text-gray-500 dark:bg-[#1f2021] dark:text-gray-400">
                                    {{ $rank + 1 }}
                                </div>
                                <img
                                    src="{{ $member->passport ? asset($member->passport) : '/avatar1.jpg' }}"
                                    alt="{{ $displayName($member) }}"
                                    class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm"
                                    onerror="this.onerror=null;this.src='/avatar1.jpg';"
                                >
                                <div class="min-w-0 flex-1">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <p class="min-w-0 truncate text-sm font-semibold text-gray-900 dark:text-white sm:text-base">{{ $displayName($member) }}</p>
                                        @if ((int) $member->verified_status === 1)
                                            <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-sky-100 text-[9px] font-bold text-sky-600 dark:bg-sky-500/10 dark:text-sky-300">✓</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-4 text-center">
                                <div>
                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($member->total_points) }}</p>
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-slate-500">Points</p>
                                </div>
                                <div class="hidden sm:block">
                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($member->posts_count) }}</p>
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-slate-500">Posts</p>
                                </div>
                                <div class="hidden sm:block">
                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($member->replies_count) }}</p>
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-slate-500">Replies</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            No monthly community activity yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $monthlyLeaders->links('pagination.community') }}
                </div>
            </section>

            <section
                x-show="activeTab === 'top-members'"
                x-transition.opacity.duration.200ms
                x-cloak
            >
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Top Members</h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Highest point totals across all-time community activity.</p>
                </div>

                @if ($topMembers->currentPage() === 1 && $topMembers->count() > 0)
                    <div class="mt-4">
                        @include('community.partials.leaderboard-podium', [
                            'members' => $topMembers->take(3),
                            'displayName' => $displayName,
                            'palette' => 'top-members',
                            'stats' => [
                                ['key' => 'total_points', 'label' => 'Points'],
                                ['key' => 'comments_count', 'label' => 'Comments'],
                            ],
                        ])
                    </div>
                @endif

                <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#161617] {{ $topMembers->currentPage() === 1 && $topMembers->total() <= 3 ? 'hidden' : '' }}">
                    @php $skipTop3Members = $topMembers->currentPage() === 1; @endphp
                    @forelse ($topMembers as $index => $member)
                        @php $rank = $topMembers->firstItem() + $index - 1; @endphp
                        @continue($skipTop3Members && $rank < 3)
                        <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-5 transition-colors hover:bg-gray-50 dark:hover:bg-white/[0.03] {{ $loop->last ? '' : 'border-b border-gray-100 dark:border-white/10' }}">
                            <div class="flex min-w-0 flex-1 items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold bg-gray-100 text-gray-500 dark:bg-[#1f2021] dark:text-gray-400">
                                    {{ $rank + 1 }}
                                </div>
                                <img
                                    src="{{ $member->passport ? asset($member->passport) : '/avatar1.jpg' }}"
                                    alt="{{ $displayName($member) }}"
                                    class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm"
                                    onerror="this.onerror=null;this.src='/avatar1.jpg';"
                                >
                                <div class="min-w-0 flex-1">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <p class="min-w-0 truncate text-sm font-semibold text-gray-900 dark:text-white sm:text-base">{{ $displayName($member) }}</p>
                                        @if ((int) $member->verified_status === 1)
                                            <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-sky-100 text-[9px] font-bold text-sky-600 dark:bg-sky-500/10 dark:text-sky-300">✓</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-4 text-center">
                                <div>
                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($member->total_points) }}</p>
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-slate-500">Points</p>
                                </div>
                                <div class="hidden sm:block">
                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($member->comments_count) }}</p>
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-slate-500">Comments</p>
                                </div>
                                <div class="hidden sm:block">
                                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($member->likes_count) }}</p>
                                    <p class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-slate-500">Likes</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                            No overall community activity yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $topMembers->links('pagination.community') }}
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
