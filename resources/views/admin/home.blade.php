@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1440px] space-y-6 px-4 py-6 sm:px-7 sm:py-8">
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Site Analytics</h1>
            <p class="mt-1 text-sm text-slate-600">See how people join, return, and request your free resources.</p>
        </div>
        <a href="{{ url('/admin/users') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Manage Users</a>
    </header>

    <section aria-label="Reporting period" class="rounded-2xl border border-slate-200 bg-white px-4 py-4 sm:px-5">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-bold text-slate-900">{{ $from->format('M j, Y') }} – {{ $to->format('M j, Y') }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach([7, 30, 90] as $days)
                        @php
                            $selected = $to->eq($today) && $from->eq($today->subDays($days - 1));
                        @endphp
                        <a href="{{ route('admin', ['tab' => $activeTab, 'from' => $today->subDays($days - 1)->toDateString(), 'to' => $today->toDateString()]) }}"
                           @if($selected) aria-current="true" @endif
                           class="rounded-md border px-3 py-1.5 text-xs font-bold {{ $selected ? 'border-blue-200 bg-blue-50 text-blue-800' : 'border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ $days }} days</a>
                    @endforeach
                </div>
            </div>
            <form action="{{ route('admin') }}" method="GET" class="flex flex-wrap items-end gap-2">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <label class="text-xs font-semibold text-slate-600">From
                    <input type="date" name="from" value="{{ $from->toDateString() }}" max="{{ $today->toDateString() }}" required class="mt-1 block rounded-md border border-slate-300 px-2 py-1.5 text-sm text-slate-900">
                </label>
                <label class="text-xs font-semibold text-slate-600">To
                    <input type="date" name="to" value="{{ $to->toDateString() }}" max="{{ $today->toDateString() }}" required class="mt-1 block rounded-md border border-slate-300 px-2 py-1.5 text-sm text-slate-900">
                </label>
                <button class="rounded-md bg-[#1447a6] px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Apply</button>
            </form>
        </div>
        @if($rangeError)
            <p role="alert" class="mt-2 text-sm text-red-700">{{ $rangeError }} Showing the last 30 days.</p>
        @endif
    </section>

    <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($stats as $label => [$icon, $value, $description])
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                        <i class="fas fa-{{ $icon }}" aria-hidden="true"></i>
                    </span>
                    @if($label === 'Active subscribers')
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">Today</span>
                    @endif
                </div>
                <dt class="text-sm font-semibold text-slate-700">{{ $label }}</dt>
                <dd class="mt-0.5 text-3xl font-extrabold tabular-nums text-slate-900">{{ number_format($value) }}</dd>
                <p class="mt-1 text-xs leading-5 text-slate-600">{{ $description }}</p>
            </div>
        @endforeach
    </dl>

    <nav aria-label="Analytics sections" class="flex flex-wrap gap-1 rounded-xl border border-slate-200 bg-white p-1.5">
        @foreach($tabs as $tab => $label)
            <a href="{{ route('admin', ['tab' => $tab, 'from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
               @if($activeTab === $tab) aria-current="page" @endif
               class="rounded-lg px-3 py-2 text-sm font-semibold {{ $activeTab === $tab ? 'bg-[#1447a6] text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    @if($activeTab === 'overview')
        <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <h2 class="text-lg font-bold text-slate-900">Daily member sign-ins</h2>
            <p class="mt-1 text-sm text-slate-600">Each member counts once per day when they log in. Dates use {{ config('app.timezone') }}.</p>
            <div class="mt-4 max-h-72 overflow-auto rounded-lg border border-slate-200" tabindex="0" role="region" aria-label="Daily sign-in counts for the selected period">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 bg-slate-50 text-xs font-semibold text-slate-700">
                        <tr>
                            <th scope="col" class="px-3 py-2.5">Date</th>
                            <th scope="col" class="px-3 py-2.5 text-right">Signed in</th>
                            <th scope="col" class="px-3 py-2.5 text-right">New sign-ups</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($dailyCounts as $row)
                            <tr>
                                <th scope="row" class="px-3 py-2.5 font-medium text-slate-800">{{ $row['date'] }}</th>
                                <td class="px-3 py-2.5 text-right font-bold tabular-nums text-blue-800">
                                    @if($row['signIns'] === null)
                                        <span aria-label="Unavailable" class="font-normal text-slate-500">—</span>
                                    @else
                                        {{ number_format($row['signIns']) }}
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-right tabular-nums text-slate-600">{{ number_format($row['signups']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-600">A dash means sign-in history wasn't being recorded yet on that day. The Active members card also counts visits from remembered sessions.</p>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-labelledby="latest-signups-heading">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 id="latest-signups-heading" class="text-lg font-bold text-slate-900">Latest 10 sign-ups</h2>
                <a href="{{ url('/admin/users') }}" class="text-sm font-semibold text-blue-800 hover:underline">All users <span aria-hidden="true">→</span></a>
            </div>
            <p class="mt-1 text-sm text-slate-600">Newest member accounts, with the plan and access they have today.</p>
            @if($latestSignups->isEmpty())
                <p class="mt-4 rounded-lg bg-slate-50 px-3 py-6 text-center text-sm text-slate-600">No members have signed up yet.</p>
            @else
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full min-w-[40rem] text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-700">
                            <tr>
                                <th scope="col" class="px-3 py-2">Name</th>
                                <th scope="col" class="px-3 py-2">Email</th>
                                <th scope="col" class="px-3 py-2">Joined</th>
                                <th scope="col" class="px-3 py-2">Country</th>
                                <th scope="col" class="px-3 py-2">Plan</th>
                                <th scope="col" class="px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($latestSignups as $signup)
                                <tr>
                                    <td class="px-3 py-3 font-semibold">
                                        <a href="{{ route('admin.users.manage', $signup->id) }}" class="text-slate-900 hover:text-blue-800 hover:underline">{{ $signup->name }}</a>
                                    </td>
                                    <td class="break-all px-3 py-3 text-slate-700">{{ $signup->email }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-700">
                                        <time datetime="{{ $signup->joined->toIso8601String() }}">{{ $signup->joined->format('M j, Y') }}</time>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700">{{ $signup->country ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-700">{{ $signup->plan }}</td>
                                    <td class="whitespace-nowrap px-3 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ match ($signup->status) { 'Active' => 'bg-emerald-50 text-emerald-800', 'Canceled' => 'bg-amber-50 text-amber-800', default => 'bg-slate-100 text-slate-700' } }}">{{ $signup->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @elseif($activeTab === 'acquisition')
        <div class="grid gap-4 xl:grid-cols-2">
            @include('admin.partials.country-breakdown', [
                'headingId' => 'period-countries-heading',
                'heading' => 'Sign-ups by country',
                'intro' => 'Member accounts created between ' . $from->format('M j') . ' and ' . $to->format('M j, Y') . '.',
                'breakdown' => $countriesPeriod,
                'empty' => 'No sign-ups in this period.',
            ])
            @include('admin.partials.country-breakdown', [
                'headingId' => 'all-countries-heading',
                'heading' => 'All members by country',
                'intro' => 'Every registered member, paid or not, across all dates.',
                'breakdown' => $countriesAllTime,
                'empty' => 'No members yet.',
            ])
        </div>

        <section class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 sm:p-5" aria-labelledby="traffic-sources-heading">
            <h2 id="traffic-sources-heading" class="text-lg font-bold text-slate-900">Traffic sources</h2>
            <p class="mt-1 text-sm leading-6 text-slate-600">Not tracked yet. The site doesn't record where visitors come from or how many visit, so this report can't be shown. Recording the referrer and UTM tags when someone signs up would make it possible.</p>
        </section>
    @elseif($activeTab === 'student-engagement')
        @include('admin.partials.student-engagement')

        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-labelledby="assessment-level-heading">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 id="assessment-level-heading" class="text-lg font-bold text-slate-900">Student skill levels</h2>
                    <p class="mt-1 text-sm text-slate-600">Latest assessment quiz result per student.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">All time</span>
            </div>
            <dl class="mt-4 grid gap-3 sm:grid-cols-3">
                @foreach($skillLevels as $level => $count)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <dt class="text-sm font-semibold text-slate-700">{{ $level }}</dt>
                        <dd class="mt-1 text-2xl font-extrabold tabular-nums text-[#1447a6]">{{ number_format($count) }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <h2 class="text-lg font-bold text-slate-900">Free resource activity</h2>
            <p class="mt-1 text-sm text-slate-600">Email requests by guide</p>
            <ul class="mt-3 divide-y divide-slate-100">
                @forelse($guides as $guide)
                    <li class="flex justify-between gap-3 py-2.5 text-sm">
                        <span>{{ config('piano-guides')[$guide->guide_slug]['title'] ?? str($guide->guide_slug)->replace('-', ' ')->title() }}</span>
                        <strong class="tabular-nums text-slate-900">{{ number_format($guide->total) }}</strong>
                    </li>
                @empty
                    <li class="py-2.5 text-sm text-slate-600">No guide requests in this period.</li>
                @endforelse
            </ul>
            <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2.5 text-xs leading-5 text-slate-600"><strong>PDF opens and downloads</strong> · Tracking data unavailable. Guide requests and roadmap leads are measured from submitted emails.</p>
        </section>
    @elseif($activeTab === 'subscriptions')
        @include('admin.partials.subscription-activity')
    @endif
</div>
@endsection
