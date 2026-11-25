@extends('layouts.hub')

@section('title', 'Leaderboard')

@section('breadcrumbs')
    @include('community.partials.breadcrumbs', ['items' => [['label' => 'Leaderboard']]])
@endsection

@section('content')
<div class="min-h-full bg-gray-50 px-4 py-6 dark:bg-black sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl space-y-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Community Leaderboard</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Member rankings based on lesson completions and community activity.</p>
            </div>
            <nav aria-label="Leaderboard period" class="inline-flex shrink-0 self-start rounded-xl border border-gray-200 bg-white p-1 dark:border-white/10 dark:bg-gray-900 sm:self-auto">
                <a href="{{ route('community.leaderboard') }}" @if(!$allTime) aria-current="page" @endif
                   class="rounded-lg px-4 py-2 text-sm font-semibold {{ !$allTime ? 'bg-[#1447A6] text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/10' }}">
                    This Month
                </a>
                <a href="{{ route('community.leaderboard', ['period' => 'all']) }}" @if($allTime) aria-current="page" @endif
                   class="rounded-lg px-4 py-2 text-sm font-semibold {{ $allTime ? 'bg-[#1447A6] text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/10' }}">
                    All Time
                </a>
            </nav>
        </header>

        <section aria-labelledby="ranking-heading" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-4 py-4 dark:border-white/10 sm:px-6">
                <h2 id="ranking-heading" class="text-base font-bold text-gray-900 dark:text-white">{{ $allTime ? 'All-Time Rankings' : $month->format('F Y') }}</h2>
            </div>
            @if($leaders->isNotEmpty())
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-600 dark:bg-white/5 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="w-14 px-3 py-3 text-center sm:w-20 sm:px-5">Rank</th>
                            <th scope="col" class="py-3 pr-2">Member</th>
                            <th scope="col" class="hidden px-3 py-3 text-right sm:table-cell">Lessons Completed</th>
                            <th scope="col" class="hidden px-3 py-3 text-right md:table-cell">Posts</th>
                            <th scope="col" class="px-3 py-3 text-right sm:px-6">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @foreach($leaders as $member)
                            @php
                                $rank = $leaders->firstItem() + $loop->index;
                                $name = trim($member->display_name ?? '') ?: (ltrim($member->user_name ?? '', '@') ?: 'Community member');
                                $isCurrentMember = (int) $member->id === (int) auth()->id();
                            @endphp
                            <tr @class(['bg-blue-50 dark:bg-blue-500/10' => $isCurrentMember])>
                                <td class="px-3 py-3 text-center sm:px-5">
                                    <span class="inline-flex min-w-8 items-center justify-center rounded-full px-2 py-1 text-xs font-bold tabular-nums {{ $rank <= 3 ? 'bg-blue-100 text-blue-900 dark:bg-blue-500/20 dark:text-blue-200' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $rank }}
                                    </span>
                                </td>
                                <th scope="row" class="min-w-0 py-3 pr-2 text-left font-semibold text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <img src="{{ $member->passport ? asset($member->passport) : asset('avatar1.jpg') }}"
                                             alt="" width="36" height="36" loading="lazy"
                                             class="h-9 w-9 shrink-0 rounded-full bg-gray-100 object-cover"
                                             onerror="this.onerror=null;this.src='{{ asset('avatar1.jpg') }}';">
                                        <div class="min-w-0 break-words [overflow-wrap:anywhere]">
                                            {{ $name }}
                                            @if((int) $member->verified_status === 1)
                                                <span class="text-blue-700 dark:text-blue-300" aria-label="Verified member" title="Verified member">✓</span>
                                            @endif
                                            @if($isCurrentMember)
                                                <span class="ml-1 text-xs text-[#1447A6] dark:text-blue-300">You</span>
                                            @endif
                                        </div>
                                    </div>
                                </th>
                                <td class="hidden px-3 py-3 text-right tabular-nums text-gray-600 dark:text-gray-300 sm:table-cell">{{ number_format($member->completed_count) }}</td>
                                <td class="hidden px-3 py-3 text-right tabular-nums text-gray-600 dark:text-gray-300 md:table-cell">{{ number_format($member->posts_count) }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-right text-base font-bold tabular-nums text-[#1447A6] dark:text-blue-300 sm:px-6">{{ number_format($member->total_points) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fa-solid fa-ranking-star text-2xl text-[#1447A6] dark:text-blue-300" aria-hidden="true"></i>
                    <p class="mt-3 text-base font-bold text-gray-900 dark:text-white">{{ $allTime ? 'No rankings yet' : 'No rankings this month yet' }}</p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Members appear here once they earn points.</p>
                </div>
            @endif
        </section>

        {{ $leaders->links('pagination.community') }}

        <details class="rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600 dark:border-white/10 dark:bg-gray-900 dark:text-gray-300">
            <summary class="cursor-pointer font-semibold text-gray-900 dark:text-white">How points are earned</summary>
            <p class="mt-2 leading-relaxed">Earn 10 points per roadmap lesson completed, 5 per post, 3 per comment, 2 per reply, and 1 per post like you give. Roadmap milestones award 20 bonus points each.</p>
        </details>
    </div>
</div>
@endsection
