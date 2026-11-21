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
                    @if($label === 'Visitors')
                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">Demo data</span>
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
            @php
                $showDemoSignIns = collect($dailyCounts)->every(fn ($row) => $row['signIns'] === null);
                $demoSignIns = [34, 26, 31, 19, 24, 16, 22, 29, 18, 27, 15];
            @endphp
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-slate-900">Daily member sign-ins</h2>
                @if($showDemoSignIns)
                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">Demo sign-ins</span>
                @endif
            </div>
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
                                    @if($showDemoSignIns)
                                        {{ $demoSignIns[$loop->index % count($demoSignIns)] }}
                                    @elseif($row['signIns'] === null)
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
            <p class="mt-3 text-xs leading-5 text-slate-600">
                @if($showDemoSignIns)
                    The Signed in column shows sample counts for this period. New sign-ups come from registration records.
                @else
                    A dash means sign-in history is unavailable for that day. The separate Active members card includes visits from remembered sessions.
                @endif
            </p>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            @php
                $demoSignups = [
                    ['Amara Okafor', 'amara.okafor@example.test', 0, 'Nigeria', 'Standard Monthly'],
                    ['Daniel Mensah', 'daniel.mensah@example.test', 0, 'Ghana', null],
                    ['Sofia Bennett', 'sofia.bennett@example.test', 1, 'United Kingdom', 'Standard Yearly'],
                    ['Ethan Brooks', 'ethan.brooks@example.test', 1, 'United States', null],
                    ['Maya Chen', 'maya.chen@example.test', 2, 'Canada', 'Premium Quarterly'],
                    ['Chidi Nwosu', 'chidi.nwosu@example.test', 2, 'Nigeria', null],
                    ['Isabel Santos', 'isabel.santos@example.test', 3, 'Philippines', 'Standard Monthly'],
                    ['Noah Wilson', 'noah.wilson@example.test', 4, 'Australia', null],
                    ['Leila Mbeki', 'leila.mbeki@example.test', 4, 'South Africa', 'Premium Yearly'],
                    ['Ava Thompson', 'ava.thompson@example.test', 5, 'United States', null],
                ];
            @endphp
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-lg font-bold text-slate-900">Latest 10 sign-ups</h2>
                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">Demo data</span>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr><th scope="col" class="px-3 py-2">Name</th><th scope="col" class="px-3 py-2">Email</th><th scope="col" class="px-3 py-2">Joined</th><th scope="col" class="px-3 py-2">Sign-up country</th><th scope="col" class="px-3 py-2">Sign-up type</th><th scope="col" class="px-3 py-2">Plan</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($demoSignups as [$name, $email, $daysAgo, $country, $plan])
                            <tr>
                                <td class="px-3 py-3 font-semibold">{{ $name }}</td>
                                <td class="px-3 py-3">{{ $email }}</td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $today->subDays($daysAgo)->format('M j, Y') }}</td>
                                <td class="px-3 py-3">{{ $country }}</td>
                                <td class="whitespace-nowrap px-3 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $plan ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $plan ? 'Paid at sign-up' : 'Sign-up only' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3">{{ $plan ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @elseif($activeTab === 'acquisition')
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-labelledby="traffic-sources-heading">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <h2 id="traffic-sources-heading" class="text-lg font-bold text-slate-900">Traffic sources and conversions</h2>
                    <p class="mt-1 text-xs text-slate-600">Sample counts for the selected period. Paid plans are included in accounts created.</p>
                </div>
                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">Demo data</span>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full min-w-[18rem] text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-700">
                        <tr>
                            <th scope="col" class="px-2 py-2 sm:px-3">Source</th>
                            <th scope="col" class="px-2 py-2 text-right sm:px-3">Visitors</th>
                            <th scope="col" class="px-2 py-2 text-right sm:px-3">Accounts created</th>
                            <th scope="col" class="px-2 py-2 text-right sm:px-3">Paid plans</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($demoTraffic as $source => $counts)
                            <tr>
                                <th scope="row" class="px-2 py-2 font-medium text-slate-800 sm:px-3">
                                    {{ $source }}
                                    <div class="mt-1 h-1.5 rounded-full bg-slate-100" aria-hidden="true">
                                        <div class="h-full rounded-full bg-blue-600" style="width: {{ 100 * $counts['visitors'] / $demoVisitors }}%"></div>
                                    </div>
                                </th>
                                <td class="px-2 py-2 text-right font-semibold tabular-nums text-slate-900 sm:px-3">{{ number_format($counts['visitors']) }}</td>
                                <td class="px-2 py-2 text-right tabular-nums text-slate-700 sm:px-3">{{ number_format($counts['accounts']) }}</td>
                                <td class="px-2 py-2 text-right tabular-nums text-slate-700 sm:px-3">{{ number_format($counts['paidPlans']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-slate-200 bg-slate-50 font-bold tabular-nums text-slate-900">
                        <tr>
                            <th scope="row" class="px-2 py-2 sm:px-3">Total</th>
                            <td class="px-2 py-2 text-right sm:px-3">{{ number_format($demoVisitors) }}</td>
                            <td class="px-2 py-2 text-right sm:px-3">{{ number_format(array_sum(array_column($demoTraffic, 'accounts'))) }}</td>
                            <td class="px-2 py-2 text-right sm:px-3">{{ number_format(array_sum(array_column($demoTraffic, 'paidPlans'))) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Members by country</h2>
                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">Demo data</span>
            </div>
            <p class="mt-1 text-xs text-slate-600">All registered members, including paid and unpaid accounts, across all dates.</p>
            <div class="mt-4 flex justify-between border-b border-slate-200 pb-2 text-xs font-semibold text-slate-600">
                <span>Country</span>
                <span>Members</span>
            </div>
            <ul class="mt-3 space-y-3">
                @foreach(['Nigeria' => 48, 'United States' => 27, 'United Kingdom' => 16, 'Ghana' => 11, 'Canada' => 8] as $country => $count)
                    <li>
                        <div class="mb-1 flex justify-between gap-2 text-sm text-slate-800">
                            <span>{{ $country }}</span>
                            <strong class="tabular-nums">{{ $count }}</strong>
                        </div>
                        <div class="h-1.5 rounded-full bg-slate-100" aria-hidden="true">
                            <div class="h-full rounded-full bg-blue-600" style="width: {{ $count / 48 * 100 }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
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
