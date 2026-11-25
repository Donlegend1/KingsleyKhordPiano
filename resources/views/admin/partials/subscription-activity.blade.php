@php
    $activity = $subscriptionActivity;
    $endedTotal = $activity['endedCancelled'] + $activity['endedOther'];
@endphp

<section aria-labelledby="subscription-activity-heading" class="space-y-4">
    <header>
        <h2 id="subscription-activity-heading" class="text-xl font-bold text-slate-900">Subscription Activity</h2>
        <p class="mt-1 text-sm text-slate-600">From subscription records for Stripe, PayPal and Paystack. Counts are unique members.</p>
    </header>

    <div>
        <h3 class="text-sm font-bold text-slate-700">{{ $from->format('M j') }} – {{ $to->format('M j, Y') }}</h3>
        <dl class="mt-2 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach([
                ['Subscriptions started', $activity['started'], 'Members who started a subscription in this period'],
                ['Ended after cancelling', $activity['endedCancelled'], 'Cancelled subscriptions whose paid term ran out'],
                ['Ended for another reason', $activity['endedOther'], 'Usually an unpaid renewal'],
            ] as [$label, $count, $description])
                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                    <dt class="text-sm font-semibold text-slate-700">{{ $label }}</dt>
                    <dd class="mt-1 text-3xl font-extrabold tabular-nums text-[#1447a6]">{{ number_format($count) }}</dd>
                    <dd class="mt-1 text-xs leading-5 text-slate-600">{{ $description }}</dd>
                </div>
            @endforeach
        </dl>
    </div>

    <div>
        <h3 class="text-sm font-bold text-slate-700">Right now</h3>
        <dl class="mt-2 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach([
                ['Active subscribers', $activity['activeSubscribers'], 'Members with plan access today'],
                ['Cancelled, access continues', $activity['cancelledWithAccess'], 'Cancelled but still inside their paid term'],
                ['Payment past due', $activity['pastDue'], 'A renewal charge failed and hasn\'t been paid yet'],
            ] as [$label, $count, $description])
                <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                    <dt class="text-sm font-semibold text-slate-700">{{ $label }}</dt>
                    <dd class="mt-1 text-3xl font-extrabold tabular-nums text-slate-900">{{ number_format($count) }}</dd>
                    <dd class="mt-1 text-xs leading-5 text-slate-600">{{ $description }}</dd>
                </div>
            @endforeach
        </dl>
    </div>

    @if($endedTotal > 0)
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-labelledby="why-ended-heading">
            <h3 id="why-ended-heading" class="text-lg font-bold text-slate-900">Why subscriptions ended</h3>
            <p class="mt-1 text-sm text-slate-600">{{ number_format($endedTotal) }} {{ Str::plural('subscription', $endedTotal) }} ended in this period.</p>
            <dl class="mt-4 space-y-3">
                @foreach([
                    ['After the member cancelled', $activity['endedCancelled'], 'bg-blue-600'],
                    ['Another reason, usually an unpaid renewal', $activity['endedOther'], 'bg-amber-500'],
                ] as [$reason, $count, $color])
                    <div>
                        <div class="flex justify-between gap-3 text-sm text-slate-800">
                            <dt>{{ $reason }}</dt>
                            <dd class="font-bold tabular-nums">{{ number_format($count) }} <span class="text-xs font-normal text-slate-600">({{ round($count / $endedTotal * 100) }}%)</span></dd>
                        </div>
                        <div class="mt-1.5 h-1.5 rounded-full bg-slate-100" aria-hidden="true">
                            <div class="h-full rounded-full {{ $color }}" style="width: {{ round($count / $endedTotal * 100, 1) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </dl>
        </section>
    @endif

    <p class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs leading-5 text-slate-600">Cancel-button clicks and individual renewal charges aren't recorded with dates, so they aren't reported here.</p>
</section>
