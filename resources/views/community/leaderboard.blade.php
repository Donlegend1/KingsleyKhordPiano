@extends('layouts.community')

@section('content')
<div x-data="{ activeTab: 'leaderboard' }" class="min-h-full bg-[radial-gradient(circle_at_top,#eef4ff,transparent_40%),linear-gradient(180deg,#f8fafc_0%,#eef2f7_100%)] px-4 py-6 dark:bg-[linear-gradient(180deg,#111827_0%,#0f172a_100%)] sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="overflow-hidden rounded-[28px] border border-white/70 bg-white/90 shadow-[0_24px_80px_rgba(15,23,42,0.08)] backdrop-blur dark:border-white/10 dark:bg-slate-900/90">
            <div class="border-b border-slate-200/70 px-6 py-6 dark:border-slate-800">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="space-y-2">
                        <span class="inline-flex w-fit items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.28em] text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                            Community Rankings
                        </span>
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Leaderboards</h1>
                            <p class="mt-2 max-w-2xl text-sm text-slate-600 dark:text-slate-300">
                                Track the most active members in the community. Leaderboard shows the highest point earners this month, while Top Members ranks all-time point totals.
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-100/80 p-1 dark:bg-slate-800/80">
                        <div class="flex flex-wrap gap-1">
                            <button
                                type="button"
                                @click="activeTab = 'leaderboard'"
                                :class="activeTab === 'leaderboard' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white'"
                                class="rounded-xl px-4 py-2 text-sm font-semibold transition"
                            >
                                Leaderboard
                            </button>
                            <button
                                type="button"
                                @click="activeTab = 'top-members'"
                                :class="activeTab === 'top-members' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white'"
                                class="rounded-xl px-4 py-2 text-sm font-semibold transition"
                            >
                                Top Members
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-6 xl:grid-cols-[320px,1fr]">
                <div class="space-y-5">
                    <div
                        x-show="activeTab === 'leaderboard'"
                        x-transition.opacity.duration.200ms
                        x-cloak
                        class="min-h-[280px] rounded-[24px] bg-gradient-to-br from-amber-400 via-orange-400 to-rose-500 p-6 text-white shadow-[0_24px_60px_rgba(249,115,22,0.35)]"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/80">Monthly Champion</p>
                        @if ($monthlyLeaders->isNotEmpty())
                            @php $champion = $monthlyLeaders->first(); @endphp
                            <div class="mt-6 flex items-center gap-4">
                                <img
                                    src="{{ $champion->passport ?: '/avatar1.jpg' }}"
                                    alt="{{ trim($champion->first_name . ' ' . $champion->last_name) }}"
                                    class="h-20 w-20 rounded-3xl border-4 border-white/30 object-cover shadow-lg"
                                >
                                <div>
                                    <p class="text-2xl font-bold leading-tight">{{ trim($champion->first_name . ' ' . $champion->last_name) }}</p>
                                    <p class="mt-1 text-sm text-white/80">{{ $champion->user_name ? '@' . $champion->user_name : 'Community member' }}</p>
                                </div>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-2xl bg-white/15 px-4 py-3">
                                    <p class="text-white/70">Points</p>
                                    <p class="mt-1 text-2xl font-bold">{{ number_format($champion->total_points) }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/15 px-4 py-3">
                                    <p class="text-white/70">Posts</p>
                                    <p class="mt-1 text-2xl font-bold">{{ number_format($champion->posts_count) }}</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-4 text-sm text-white/85">No monthly activity yet. Rankings will update as members interact in the community.</p>
                        @endif
                    </div>

                    <div
                        x-show="activeTab === 'top-members'"
                        x-transition.opacity.duration.200ms
                        x-cloak
                        class="min-h-[280px] rounded-[24px] bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 p-6 text-white shadow-[0_24px_60px_rgba(15,23,42,0.28)]"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-200/80">All-Time Standout</p>
                        @if ($topMembers->isNotEmpty())
                            @php $allTimeChampion = $topMembers->first(); @endphp
                            <div class="mt-6 flex items-center gap-4">
                                <img
                                    src="{{ $allTimeChampion->passport ?: '/avatar1.jpg' }}"
                                    alt="{{ trim($allTimeChampion->first_name . ' ' . $allTimeChampion->last_name) }}"
                                    class="h-20 w-20 rounded-3xl border-4 border-white/20 object-cover shadow-lg"
                                >
                                <div>
                                    <p class="text-2xl font-bold leading-tight">{{ trim($allTimeChampion->first_name . ' ' . $allTimeChampion->last_name) }}</p>
                                    <p class="mt-1 text-sm text-slate-300">{{ $allTimeChampion->user_name ? '@' . $allTimeChampion->user_name : 'Community member' }}</p>
                                </div>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-2xl bg-white/10 px-4 py-3">
                                    <p class="text-slate-300">Points</p>
                                    <p class="mt-1 text-2xl font-bold">{{ number_format($allTimeChampion->total_points) }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 px-4 py-3">
                                    <p class="text-slate-300">Comments</p>
                                    <p class="mt-1 text-2xl font-bold">{{ number_format($allTimeChampion->comments_count) }}</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-4 text-sm text-slate-300">No overall activity yet. Top members will appear once the community starts engaging.</p>
                        @endif
                    </div>

                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Points Breakdown</h2>
                        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                                <span>Post created</span>
                                <strong>10 pts</strong>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                                <span>Comment added</span>
                                <strong>5 pts</strong>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                                <span>Reply posted</span>
                                <strong>3 pts</strong>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                                <span>Like given</span>
                                <strong>1 pt</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <section
                        x-show="activeTab === 'leaderboard'"
                        x-transition.opacity.duration.200ms
                        x-cloak
                        class="min-h-[880px]"
                    >
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Leaderboard</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Highest point totals for {{ now()->format('F Y') }}.</p>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-[24px] border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                            @forelse ($monthlyLeaders as $index => $member)
                                <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between {{ $loop->last ? 'border-b-0' : '' }}">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $index < 3 ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }} text-lg font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <img
                                            src="{{ $member->passport ?: '/avatar1.jpg' }}"
                                            alt="{{ trim($member->first_name . ' ' . $member->last_name) }}"
                                            class="h-14 w-14 rounded-2xl object-cover"
                                        >
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-semibold text-slate-900 dark:text-white">{{ trim($member->first_name . ' ' . $member->last_name) }}</p>
                                                @if ((int) $member->verified_status === 1)
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-sky-100 text-[10px] font-bold text-sky-600 dark:bg-sky-500/10 dark:text-sky-300">✓</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $member->user_name ? '@' . $member->user_name : 'Community member' }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 sm:flex sm:items-center">
                                        <div class="rounded-2xl bg-slate-100 px-4 py-2 text-center dark:bg-slate-800">
                                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Points</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900 dark:text-white">{{ number_format($member->total_points) }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-slate-100 px-4 py-2 text-center dark:bg-slate-800">
                                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Posts</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900 dark:text-white">{{ number_format($member->posts_count) }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-slate-100 px-4 py-2 text-center dark:bg-slate-800">
                                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Replies</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900 dark:text-white">{{ number_format($member->replies_count) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                    No monthly community activity yet.
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section
                        x-show="activeTab === 'top-members'"
                        x-transition.opacity.duration.200ms
                        x-cloak
                        class="min-h-[880px]"
                    >
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Top Members</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Highest point totals across all-time community activity.</p>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-[24px] border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                            @forelse ($topMembers as $index => $member)
                                <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between {{ $loop->last ? 'border-b-0' : '' }}">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $index < 3 ? 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }} text-lg font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <img
                                            src="{{ $member->passport ?: '/avatar1.jpg' }}"
                                            alt="{{ trim($member->first_name . ' ' . $member->last_name) }}"
                                            class="h-14 w-14 rounded-2xl object-cover"
                                        >
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-semibold text-slate-900 dark:text-white">{{ trim($member->first_name . ' ' . $member->last_name) }}</p>
                                                @if ((int) $member->verified_status === 1)
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-sky-100 text-[10px] font-bold text-sky-600 dark:bg-sky-500/10 dark:text-sky-300">✓</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $member->user_name ? '@' . $member->user_name : 'Community member' }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 sm:flex sm:items-center">
                                        <div class="rounded-2xl bg-slate-100 px-4 py-2 text-center dark:bg-slate-800">
                                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Points</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900 dark:text-white">{{ number_format($member->total_points) }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-slate-100 px-4 py-2 text-center dark:bg-slate-800">
                                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Comments</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900 dark:text-white">{{ number_format($member->comments_count) }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-slate-100 px-4 py-2 text-center dark:bg-slate-800">
                                            <p class="text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Likes</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900 dark:text-white">{{ number_format($member->likes_count) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                    No overall community activity yet.
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
