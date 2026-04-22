@extends('layouts.community')

@section('content')
@php
    $displayUserName = static fn ($value) => $value ? ltrim($value, '@') : 'Community member';
    $rankStyles = [
        0 => [
            'badge_style' => 'background: linear-gradient(135deg, #fde68a 0%, #fbbf24 52%, #f59e0b 100%); color: #78350f; box-shadow: 0 10px 28px rgba(245, 158, 11, 0.28);',
            'row_style' => 'background: linear-gradient(90deg, rgba(255,247,204,0.98) 0%, rgba(255,255,255,1) 46%, rgba(255,251,235,0.95) 100%); border-bottom-color: transparent;',
            'medal' => 'text-yellow-500',
            'label' => '1st',
            'chip_style' => 'background: rgba(254, 243, 199, 0.95); color: #92400e;',
        ],
        1 => [
            'badge_style' => 'background: linear-gradient(135deg, #f8fafc 0%, #cbd5e1 52%, #94a3b8 100%); color: #334155; box-shadow: 0 10px 24px rgba(148, 163, 184, 0.24);',
            'row_style' => 'background: linear-gradient(90deg, rgba(241,245,249,0.98) 0%, rgba(255,255,255,1) 46%, rgba(248,250,252,0.96) 100%); border-bottom-color: transparent;',
            'medal' => 'text-slate-400',
            'label' => '2nd',
            'chip_style' => 'background: rgba(241, 245, 249, 0.96); color: #475569;',
        ],
        2 => [
            'badge_style' => 'background: linear-gradient(135deg, #fdba74 0%, #c2410c 58%, #7c2d12 100%); color: #fff7ed; box-shadow: 0 10px 24px rgba(194, 65, 12, 0.26);',
            'row_style' => 'background: linear-gradient(90deg, rgba(255,237,213,0.98) 0%, rgba(255,255,255,1) 46%, rgba(255,247,237,0.95) 100%); border-bottom-color: transparent;',
            'medal' => 'text-orange-500',
            'label' => '3rd',
            'chip_style' => 'background: rgba(255, 237, 213, 0.96); color: #9a3412;',
        ],
    ];
@endphp
<div
    x-data="{
        activeTab: 'leaderboard',
        summaryHeight: 0,
        contentHeight: 0,
        syncHeights() {
            this.$nextTick(() => {
                const summaryHeights = [
                    this.$refs.monthlySummary?.scrollHeight ?? 0,
                    this.$refs.topSummary?.scrollHeight ?? 0,
                ];

                const contentHeights = [
                    this.$refs.monthlyContent?.scrollHeight ?? 0,
                    this.$refs.topContent?.scrollHeight ?? 0,
                ];

                this.summaryHeight = Math.max(...summaryHeights);
                this.contentHeight = Math.max(...contentHeights);
            });
        }
    }"
    x-init="syncHeights(); window.addEventListener('resize', () => syncHeights())"
    class="min-h-full bg-[radial-gradient(circle_at_top,#eef4ff,transparent_40%),linear-gradient(180deg,#f8fafc_0%,#eef2f7_100%)] px-4 py-6 dark:bg-[linear-gradient(180deg,#111827_0%,#0f172a_100%)] sm:px-6 lg:px-8"
>
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

                    <div class="w-full rounded-2xl bg-slate-100/80 p-1 dark:bg-slate-800/80 lg:w-auto">
                        <div class="grid grid-cols-2 gap-1">
                            <button
                                type="button"
                                @click="activeTab = 'leaderboard'"
                                :class="activeTab === 'leaderboard' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white'"
                                class="w-full rounded-xl px-4 py-3 text-center text-sm font-semibold transition"
                            >
                                Leaderboard
                            </button>
                            <button
                                type="button"
                                @click="activeTab = 'top-members'"
                                :class="activeTab === 'top-members' ? 'bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white'"
                                class="w-full rounded-xl px-4 py-3 text-center text-sm font-semibold transition"
                            >
                                Top Members
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 p-6 xl:grid-cols-[320px,1fr]">
                <div class="space-y-5">
                    <div class="relative" :style="{ height: `${summaryHeight}px` }">
                    <div
                        x-ref="monthlySummary"
                        x-show="activeTab === 'leaderboard'"
                        x-transition.opacity.duration.200ms
                        x-cloak
                        class="absolute inset-0 rounded-[24px] bg-gradient-to-br from-amber-400 via-orange-400 to-rose-500 p-6 text-white shadow-[0_24px_60px_rgba(249,115,22,0.35)]"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/80">Monthly Champion</p>
                        @if ($monthlyLeaders->isNotEmpty())
                            @php $champion = $monthlyLeaders->first(); @endphp
                            <div class="mt-6 flex items-center gap-4 min-w-0">
                                <img
                                    src="{{ $champion->passport ?: '/avatar1.jpg' }}"
                                    alt="{{ trim($champion->first_name . ' ' . $champion->last_name) }}"
                                    class="h-20 w-20 shrink-0 rounded-3xl border-4 border-white/30 object-cover shadow-lg"
                                >
                                <div class="min-w-0 flex-1">
                                    <p class="overflow-hidden text-ellipsis break-all text-xl font-bold leading-tight sm:text-2xl">{{ $displayUserName($champion->user_name) }}</p>
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
                        x-ref="topSummary"
                        x-show="activeTab === 'top-members'"
                        x-transition.opacity.duration.200ms
                        x-cloak
                        class="absolute inset-0 rounded-[24px] bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 p-6 text-white shadow-[0_24px_60px_rgba(15,23,42,0.28)]"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-200/80">All-Time Standout</p>
                        @if ($topMembers->isNotEmpty())
                            @php $allTimeChampion = $topMembers->first(); @endphp
                            <div class="mt-6 flex items-center gap-4 min-w-0">
                                <img
                                    src="{{ $allTimeChampion->passport ?: '/avatar1.jpg' }}"
                                    alt="{{ trim($allTimeChampion->first_name . ' ' . $allTimeChampion->last_name) }}"
                                    class="h-20 w-20 shrink-0 rounded-3xl border-4 border-white/20 object-cover shadow-lg"
                                >
                                <div class="min-w-0 flex-1">
                                    <p class="overflow-hidden text-ellipsis break-all text-xl font-bold leading-tight sm:text-2xl">{{ $displayUserName($allTimeChampion->user_name) }}</p>
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
                    </div>

                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
                        <h2 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-slate-400">Points Breakdown</h2>
                        <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                            <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                                <span>Post created</span>
                                <strong>5 pts</strong>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                                <span>Comment added</span>
                                <strong>3 pts</strong>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                                <span>Reply posted</span>
                                <strong>2 pts</strong>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl bg-white px-4 py-3 dark:bg-slate-900">
                                <span>Like given</span>
                                <strong>1 pt</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative" :style="{ height: `${contentHeight}px` }">
                    <section
                        x-ref="monthlyContent"
                        x-show="activeTab === 'leaderboard'"
                        x-transition.opacity.duration.200ms
                        x-cloak
                        class="absolute inset-0"
                    >
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Leaderboard</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Highest point totals for {{ now()->format('F Y') }}.</p>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-[24px] border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                            @forelse ($monthlyLeaders as $index => $member)
                                @php
                                    $rankStyle = $rankStyles[$index] ?? null;
                                @endphp
                                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:px-5 {{ $loop->last ? 'border-b-0' : '' }}" @if($rankStyle) style="{{ $rankStyle['row_style'] }}" @endif>
                                    <div class="flex min-w-0 flex-1 items-center gap-4">
                                        <div class="flex h-11 w-11 min-h-11 min-w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-base font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300" @if($rankStyle) style="{{ $rankStyle['badge_style'] }}" @endif>
                                            {{ $index + 1 }}
                                        </div>
                                        <img
                                            src="{{ $member->passport ?: '/avatar1.jpg' }}"
                                            alt="{{ trim($member->first_name . ' ' . $member->last_name) }}"
                                            class="h-12 w-12 shrink-0 rounded-2xl object-cover"
                                        >
                                        <div class="min-w-0 flex-1 pr-1">
                                            <div class="flex items-center gap-2">
                                                <p class="truncate text-base font-semibold text-slate-900 dark:text-white sm:text-lg">{{ $displayUserName($member->user_name) }}</p>
                                                @if ($rankStyle)
                                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.2em]" style="{{ $rankStyle['chip_style'] }}">
                                                        <i class="fa fa-medal {{ $rankStyle['medal'] }}"></i>
                                                        {{ $rankStyle['label'] }}
                                                    </span>
                                                @endif
                                                @if ((int) $member->verified_status === 1)
                                                    <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-sky-100 text-[10px] font-bold text-sky-600 dark:bg-sky-500/10 dark:text-sky-300">✓</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid shrink-0 grid-cols-3 gap-2">
                                        <div class="flex h-16 w-16 flex-col items-center justify-center rounded-2xl bg-slate-100 px-2 text-center dark:bg-slate-800 sm:h-16 sm:w-20">
                                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Points</p>
                                            <p class="mt-1 text-xl font-bold leading-none text-slate-900 dark:text-white">{{ number_format($member->total_points) }}</p>
                                        </div>
                                        <div class="flex h-16 w-16 flex-col items-center justify-center rounded-2xl bg-slate-100 px-2 text-center dark:bg-slate-800 sm:h-16 sm:w-20">
                                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Posts</p>
                                            <p class="mt-1 text-xl font-bold leading-none text-slate-900 dark:text-white">{{ number_format($member->posts_count) }}</p>
                                        </div>
                                        <div class="flex h-16 w-16 flex-col items-center justify-center rounded-2xl bg-slate-100 px-2 text-center dark:bg-slate-800 sm:h-16 sm:w-20">
                                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Replies</p>
                                            <p class="mt-1 text-xl font-bold leading-none text-slate-900 dark:text-white">{{ number_format($member->replies_count) }}</p>
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
                        x-ref="topContent"
                        x-show="activeTab === 'top-members'"
                        x-transition.opacity.duration.200ms
                        x-cloak
                        class="absolute inset-0"
                    >
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Top Members</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Highest point totals across all-time community activity.</p>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-[24px] border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                            @forelse ($topMembers as $index => $member)
                                @php
                                    $rankStyle = $rankStyles[$index] ?? null;
                                @endphp
                                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-4 dark:border-slate-800 sm:px-5 {{ $loop->last ? 'border-b-0' : '' }}" @if($rankStyle) style="{{ $rankStyle['row_style'] }}" @endif>
                                    <div class="flex min-w-0 flex-1 items-center gap-4">
                                        <div class="flex h-11 w-11 min-h-11 min-w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-base font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300" @if($rankStyle) style="{{ $rankStyle['badge_style'] }}" @endif>
                                            {{ $index + 1 }}
                                        </div>
                                        <img
                                            src="{{ $member->passport ?: '/avatar1.jpg' }}"
                                            alt="{{ trim($member->first_name . ' ' . $member->last_name) }}"
                                            class="h-12 w-12 shrink-0 rounded-2xl object-cover"
                                        >
                                        <div class="min-w-0 flex-1 pr-1">
                                            <div class="flex items-center gap-2">
                                                <p class="truncate text-base font-semibold text-slate-900 dark:text-white sm:text-lg">{{ $displayUserName($member->user_name) }}</p>
                                                @if ($rankStyle)
                                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.2em]" style="{{ $rankStyle['chip_style'] }}">
                                                        <i class="fa fa-medal {{ $rankStyle['medal'] }}"></i>
                                                        {{ $rankStyle['label'] }}
                                                    </span>
                                                @endif
                                                @if ((int) $member->verified_status === 1)
                                                    <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-sky-100 text-[10px] font-bold text-sky-600 dark:bg-sky-500/10 dark:text-sky-300">✓</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid shrink-0 grid-cols-3 gap-2">
                                        <div class="flex h-16 w-16 flex-col items-center justify-center rounded-2xl bg-slate-100 px-2 text-center dark:bg-slate-800 sm:h-16 sm:w-20">
                                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Points</p>
                                            <p class="mt-1 text-xl font-bold leading-none text-slate-900 dark:text-white">{{ number_format($member->total_points) }}</p>
                                        </div>
                                        <div class="flex h-16 w-16 flex-col items-center justify-center rounded-2xl bg-slate-100 px-2 text-center dark:bg-slate-800 sm:h-16 sm:w-20">
                                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Comments</p>
                                            <p class="mt-1 text-xl font-bold leading-none text-slate-900 dark:text-white">{{ number_format($member->comments_count) }}</p>
                                        </div>
                                        <div class="flex h-16 w-16 flex-col items-center justify-center rounded-2xl bg-slate-100 px-2 text-center dark:bg-slate-800 sm:h-16 sm:w-20">
                                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">Likes</p>
                                            <p class="mt-1 text-xl font-bold leading-none text-slate-900 dark:text-white">{{ number_format($member->likes_count) }}</p>
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
