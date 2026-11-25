@extends('layouts.admin')

@section('content')
@php
    $paceStyles = [
        'Completed' => 'bg-emerald-50 text-emerald-700',
        'Ahead' => 'bg-violet-50 text-violet-700',
        'On track' => 'bg-blue-50 text-blue-700',
        'Behind' => 'bg-red-50 text-red-700',
        'Not started' => 'bg-gray-100 text-gray-600',
        'No lessons' => 'bg-gray-100 text-gray-600',
    ];
    $barStyles = ['Completed' => 'bg-emerald-500', 'Ahead' => 'bg-violet-500', 'On track' => 'bg-blue-600', 'Behind' => 'bg-red-500'];
    $filters = [
        'all' => 'All members',
        'pending' => 'Pending review',
        'reviewed' => 'Reviewed',
        'no_plan' => 'No plan yet',
        'ahead' => 'Ahead of plan',
        'behind' => 'Behind on plan',
        'completed' => 'Plan completed',
    ];
@endphp
<div class="p-4 sm:p-6 max-w-full">
    <nav class="mb-4 text-sm text-gray-500">Admin / <span class="text-blue-600">Guidance Requests</span></nav>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
            <div>
                <h2 class="text-base font-bold text-gray-900">Guidance Requests</h2>
                <p class="text-xs text-gray-500">
                    {{ $counts['all'] }} {{ Str::plural('member', $counts['all']) }}
                    · {{ $counts['pending'] }} pending review
                    · {{ $counts['no_plan'] }} without a plan
                    · {{ $counts['behind'] }} behind
                </p>
            </div>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-3 px-4 py-3">
            <div class="relative w-full sm:w-64">
                <span class="fa fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></span>
                <input type="search" name="q" value="{{ $search }}" placeholder="Search name or email"
                       class="w-full rounded-lg border border-gray-300 py-2 pl-8 pr-3 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <select name="filter" aria-label="Filter members" onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                @foreach($filters as $value => $label)
                    <option value="{{ $value }}" @selected($filter === $value)>{{ $label }}@if(isset($counts[$value]) && $value !== 'all') ({{ $counts[$value] }})@endif</option>
                @endforeach
            </select>
            @if($search !== '' || $filter !== 'all')
                <a href="{{ route('admin.personalized-guidance.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-800">Clear</a>
            @endif
        </form>

        @if($entries->isEmpty())
            <p class="px-4 py-10 text-center text-sm text-gray-500">No members match.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-y border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold">Member</th>
                            <th class="px-4 py-2.5 font-semibold">Request</th>
                            <th class="px-4 py-2.5 font-semibold">Plan progress</th>
                            <th class="px-4 py-2.5 font-semibold">Pace</th>
                            <th class="px-4 py-2.5 font-semibold">Submitted</th>
                            <th class="px-4 py-2.5 text-right font-semibold"><span class="sr-only">Open</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($entries as $entry)
                            @php($plan = $entry->plan)
                            <tr class="group cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('admin.personalized-guidance.show', $entry->user) }}'">
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-800" aria-hidden="true">{{ Str::upper(Str::substr((string) $entry->user->first_name, 0, 1) . Str::substr((string) $entry->user->last_name, 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.personalized-guidance.show', $entry->user) }}" class="block truncate font-semibold text-gray-900 group-hover:text-blue-700">{{ $entry->user->full_name ?: 'Unknown student' }}</a>
                                            <p class="truncate text-xs text-gray-500">{{ $entry->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-2.5">
                                    @if($entry->request)
                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $entry->request->status === 'reviewed' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $entry->request->status === 'reviewed' ? 'Reviewed' : 'Pending' }}</span>
                                    @endif
                                    @if($entry->bookings->isNotEmpty())
                                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-bold text-blue-700">Call booked</span>
                                    @endif
                                </td>
                                <td class="min-w-[11rem] px-4 py-2.5">
                                    @if($plan)
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-gray-200" role="progressbar" aria-valuenow="{{ $plan['percent'] }}" aria-valuemin="0" aria-valuemax="100">
                                                <div class="h-full rounded-full {{ $barStyles[$plan['pace']] ?? 'bg-gray-400' }}" style="width: {{ $plan['percent'] }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-800">{{ $plan['percent'] }}%</span>
                                        </div>
                                        <p class="mt-0.5 text-xs text-gray-500">{{ $plan['done'] }} of {{ $plan['total'] }} lessons</p>
                                    @else
                                        <span class="text-xs text-gray-400">No plan yet</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-2.5">
                                    @if($plan)
                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $paceStyles[$plan['pace']] ?? 'bg-gray-100 text-gray-600' }}">{{ $plan['pace'] }}</span>
                                        @if(in_array($plan['pace'], ['Ahead', 'On track', 'Behind']))
                                            <p class="mt-0.5 text-xs text-gray-500">Day {{ $plan['day'] }} of 90</p>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-2.5 text-xs text-gray-500">{{ $entry->latestAt?->diffForHumans() }}</td>
                                <td class="px-4 py-2.5 text-right text-gray-400"><span class="fa fa-chevron-right text-xs"></span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($entries->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $entries->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
