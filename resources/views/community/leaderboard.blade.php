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

    $rankStyles = [
        0 => ['badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300', 'row' => 'bg-amber-50/50 dark:bg-amber-500/[0.04]', 'border' => 'border-l-4 border-amber-300 dark:border-amber-500/40', 'label' => '1st'],
        1 => ['badge' => 'bg-slate-100 text-slate-600 dark:bg-[#1f2021] dark:text-slate-300', 'row' => 'bg-slate-50/60 dark:bg-white/[0.025]', 'border' => 'border-l-4 border-slate-300 dark:border-slate-500/40', 'label' => '2nd'],
        2 => ['badge' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/10 dark:text-orange-300', 'row' => 'bg-orange-50/50 dark:bg-orange-500/[0.03]', 'border' => 'border-l-4 border-orange-300 dark:border-orange-500/40', 'label' => '3rd'],
    ];
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[300px,1fr]">

            <!-- Champion summary -->
            <div class="min-w-0">
                <div
                    x-show="activeTab === 'leaderboard'"
                    x-transition.opacity.duration.200ms
                    x-cloak
                    class="flex flex-col rounded-2xl bg-indigo-600 p-6 text-white"
                >
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-crown text-amber-300 text-xs"></i>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">Monthly Champion</p>
                    </div>
                    @if ($monthlyChampion)
                        @php $champion = $monthlyChampion; @endphp
                        <div class="mt-5 flex items-center gap-3 min-w-0">
                            <img
                                src="{{ $champion->passport ? asset($champion->passport) : '/avatar1.jpg' }}"
                                alt="{{ $displayName($champion) }}"
                                class="h-14 w-14 shrink-0 rounded-full ring-2 ring-white/40 shadow-md object-cover"
                                onerror="this.onerror=null;this.src='/avatar1.jpg';"
                            >
                            <p class="min-w-0 flex-1 text-base font-bold leading-snug overflow-hidden" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $displayName($champion) }}</p>
                        </div>
                        <div class="mt-5 grid grid-cols-2 gap-2 text-sm">
                            <div class="rounded-xl bg-white/10 px-3 py-2.5">
                                <p class="text-indigo-200 text-xs">Points</p>
                                <p class="mt-0.5 text-xl font-bold">{{ number_format($champion->total_points) }}</p>
                            </div>
                            <div class="rounded-xl bg-white/10 px-3 py-2.5">
                                <p class="text-indigo-200 text-xs">Posts</p>
                                <p class="mt-0.5 text-xl font-bold">{{ number_format($champion->posts_count) }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-1 flex-col items-center justify-center text-center gap-3 py-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10">
                                <i class="fa fa-trophy text-xl text-white/70"></i>
                            </div>
                            <p class="text-sm text-indigo-100 max-w-[220px]">No monthly activity yet. Rankings will update as members interact in the community.</p>
                        </div>
                    @endif
                </div>

                <div
                    x-show="activeTab === 'top-members'"
                    x-transition.opacity.duration.200ms
                    x-cloak
                    class="flex flex-col rounded-2xl bg-gray-900 p-6 text-white dark:bg-[#1f2021]"
                >
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-star text-amber-300 text-xs"></i>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">All-Time Standout</p>
                    </div>
                    @if ($allTimeChampion)
                        <div class="mt-5 flex items-center gap-3 min-w-0">
                            <img
                                src="{{ $allTimeChampion->passport ? asset($allTimeChampion->passport) : '/avatar1.jpg' }}"
                                alt="{{ $displayName($allTimeChampion) }}"
                                class="h-14 w-14 shrink-0 rounded-full ring-2 ring-white/30 shadow-md object-cover"
                                onerror="this.onerror=null;this.src='/avatar1.jpg';"
                            >
                            <p class="min-w-0 flex-1 text-base font-bold leading-snug overflow-hidden" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $displayName($allTimeChampion) }}</p>
                        </div>
                        <div class="mt-5 grid grid-cols-2 gap-2 text-sm">
                            <div class="rounded-xl bg-white/10 px-3 py-2.5">
                                <p class="text-gray-400 text-xs">Points</p>
                                <p class="mt-0.5 text-xl font-bold">{{ number_format($allTimeChampion->total_points) }}</p>
                            </div>
                            <div class="rounded-xl bg-white/10 px-3 py-2.5">
                                <p class="text-gray-400 text-xs">Comments</p>
                                <p class="mt-0.5 text-xl font-bold">{{ number_format($allTimeChampion->comments_count) }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-1 flex-col items-center justify-center text-center gap-3 py-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10">
                                <i class="fa fa-star text-xl text-white/70"></i>
                            </div>
                            <p class="text-sm text-gray-300 max-w-[220px]">No overall activity yet. Top members will appear once the community starts engaging.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lists -->
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

                    <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#161617]">
                        @forelse ($monthlyLeaders as $index => $member)
                            @php
                                $rank = $monthlyLeaders->firstItem() + $index - 1;
                                $rankStyle = $rankStyles[$rank] ?? null;
                            @endphp
                            <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-5 transition-colors hover:bg-gray-50 dark:hover:bg-white/[0.03] {{ $rankStyle['row'] ?? '' }} {{ $rankStyle['border'] ?? '' }} {{ $loop->last ? '' : 'border-b border-gray-100 dark:border-white/10' }}">
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold {{ $rankStyle['badge'] ?? 'bg-gray-100 text-gray-500 dark:bg-[#1f2021] dark:text-gray-400' }}">
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

                    <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#161617]">
                        @forelse ($topMembers as $index => $member)
                            @php
                                $rank = $topMembers->firstItem() + $index - 1;
                                $rankStyle = $rankStyles[$rank] ?? null;
                            @endphp
                            <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-5 transition-colors hover:bg-gray-50 dark:hover:bg-white/[0.03] {{ $rankStyle['row'] ?? '' }} {{ $rankStyle['border'] ?? '' }} {{ $loop->last ? '' : 'border-b border-gray-100 dark:border-white/10' }}">
                                <div class="flex min-w-0 flex-1 items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold {{ $rankStyle['badge'] ?? 'bg-gray-100 text-gray-500 dark:bg-[#1f2021] dark:text-gray-400' }}">
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
</div>
@endsection
