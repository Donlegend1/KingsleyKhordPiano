{{-- Expects: $headingId, $heading, $intro, $empty, $breakdown (from DashboardMetrics::membersByCountry) --}}
@php
    $largest = max(1, $breakdown['rows'][0][1] ?? 1);
@endphp
<section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-labelledby="{{ $headingId }}">
    <h2 id="{{ $headingId }}" class="text-lg font-bold text-slate-900">{{ $heading }}</h2>
    <p class="mt-1 text-xs text-slate-600">{{ $intro }}</p>

    @if($breakdown['total'] === 0)
        <p class="mt-4 rounded-lg bg-slate-50 px-3 py-6 text-center text-sm text-slate-600">{{ $empty }}</p>
    @else
        <div class="mt-4 flex justify-between border-b border-slate-200 pb-2 text-xs font-semibold text-slate-600">
            <span>Country</span>
            <span>Members</span>
        </div>
        <ul class="mt-3 space-y-3">
            @foreach($breakdown['rows'] as [$country, $count])
                <li>
                    <div class="mb-1 flex justify-between gap-2 text-sm text-slate-800">
                        <span>{{ $country }}</span>
                        <strong class="tabular-nums">{{ number_format($count) }}</strong>
                    </div>
                    <div class="h-1.5 rounded-full bg-slate-100" aria-hidden="true">
                        <div class="h-full rounded-full bg-blue-600" style="width: {{ round($count / $largest * 100, 1) }}%"></div>
                    </div>
                </li>
            @endforeach
        </ul>
        @if($breakdown['other'] || $breakdown['notSet'])
            <dl class="mt-4 space-y-1 border-t border-slate-100 pt-3 text-sm text-slate-600">
                @if($breakdown['other'])
                    <div class="flex justify-between gap-2"><dt>Other countries</dt><dd class="tabular-nums">{{ number_format($breakdown['other']) }}</dd></div>
                @endif
                @if($breakdown['notSet'])
                    <div class="flex justify-between gap-2"><dt>Country not set</dt><dd class="tabular-nums">{{ number_format($breakdown['notSet']) }}</dd></div>
                @endif
            </dl>
        @endif
    @endif
</section>
