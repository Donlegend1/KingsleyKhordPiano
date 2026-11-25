<section aria-labelledby="subscription-activity-heading" class="space-y-4">
    <header class="flex flex-wrap items-start justify-between gap-2">
        <div>
            <h2 id="subscription-activity-heading" class="text-xl font-bold text-slate-900">Subscription Activity</h2>
            <p class="mt-1 text-sm text-slate-600">Illustrative 30-day report. Counts are unique students within each metric.</p>
        </div>
        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">Demo data</span>
    </header>

    <dl class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Automatic renewals', 146, 'Recurring payments that went through'],
            ['Returning subscribers', 18, 'Started a paid plan again after a gap'],
            ['Clicked Cancel', 24, 'Students who requested cancellation'],
            ['Failed renewal payments', 19, 'Students with a failed recurring charge'],
        ] as [$label, $count, $description])
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                <dt class="text-sm font-semibold text-slate-700">{{ $label }}</dt>
                <dd class="mt-1 text-3xl font-extrabold tabular-nums text-[#1447a6]">{{ $count }}</dd>
                <dd class="mt-1 text-xs leading-5 text-slate-600">{{ $description }}</dd>
            </div>
        @endforeach
    </dl>

    <div class="grid gap-4 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <h3 class="text-lg font-bold text-slate-900">Why subscriptions ended</h3>
            <p class="mt-1 text-sm text-slate-600">Compares students whose subscriptions actually ended during the sample period.</p>
            <dl class="mt-4 space-y-3">
                @foreach([
                    ['Ended after the student cancelled', 13, 'bg-blue-600'],
                    ['Ended after an unpaid renewal', 7, 'bg-amber-500'],
                ] as [$reason, $count, $color])
                    <div>
                        <div class="flex justify-between gap-3 text-sm text-slate-800">
                            <dt>{{ $reason }}</dt>
                            <dd class="font-bold tabular-nums">{{ $count }}</dd>
                        </div>
                        <div class="mt-1.5 h-1.5 rounded-full bg-slate-100" aria-hidden="true">
                            <div class="h-full rounded-full {{ $color }}" style="width: {{ $count * 5 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </dl>
            <p class="mt-3 text-xs leading-5 text-slate-600">A student can click Cancel and still have access until their paid term ends.</p>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <h3 class="text-lg font-bold text-slate-900">What happened after a failed payment?</h3>
            <p class="mt-1 text-sm text-slate-600">Outcomes for the 19 students with a failed renewal payment.</p>
            <dl class="mt-3 divide-y divide-slate-100 text-sm">
                @foreach([
                    ['Payment recovered', 8],
                    ['Awaiting retry or payment update', 4],
                    ['Subscription ended unpaid', 7],
                ] as [$result, $count])
                    <div class="flex justify-between gap-3 py-2.5">
                        <dt class="text-slate-700">{{ $result }}</dt>
                        <dd class="font-bold tabular-nums text-slate-900">{{ $count }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </div>
</section>
