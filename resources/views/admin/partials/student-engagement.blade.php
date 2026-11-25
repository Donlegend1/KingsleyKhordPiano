@php
    $cohort = $firstWeek['cohort'];
@endphp

<section aria-labelledby="engagement-heading" class="space-y-4">
    <header>
        <h2 id="engagement-heading" class="text-xl font-bold text-slate-900">Student Engagement</h2>
        <p class="mt-1 text-sm text-slate-600">Based on members who joined between {{ $from->format('M j') }} and {{ $to->format('M j, Y') }}.</p>
    </header>

    <div class="grid gap-4 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-labelledby="first-week-heading">
            <h3 id="first-week-heading" class="text-lg font-bold text-slate-900">First-week progress</h3>
            @if($cohort === 0)
                <p class="mt-1 text-sm text-slate-600">What new members did within seven days of joining.</p>
                <p class="mt-4 rounded-lg bg-slate-50 px-3 py-6 text-center text-sm text-slate-600">No members in this period have had a full first week yet. Members count here once they joined at least seven days ago.</p>
            @else
                <p class="mt-1 text-sm text-slate-600">What {{ number_format($cohort) }} new {{ Str::plural('member', $cohort) }} did within seven days of joining. Members who joined in the last seven days aren't included yet.</p>
                <dl class="mt-4 space-y-3">
                    @foreach($firstWeek['steps'] as $action => $count)
                        @php
                            $rate = (int) round($count / $cohort * 100);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <dt>{{ $action }}</dt>
                                <dd class="shrink-0 font-semibold tabular-nums text-slate-900">{{ number_format($count) }} <span class="ml-1 text-xs font-normal text-slate-600">({{ $rate }}%)</span></dd>
                            </div>
                            <div class="mt-1.5 h-1.5 rounded-full bg-slate-100" aria-hidden="true">
                                <div class="h-full rounded-full bg-blue-600" style="width: {{ $rate }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </dl>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-labelledby="return-heading">
            <h3 id="return-heading" class="text-lg font-bold text-slate-900">Return to learning</h3>
            <p class="mt-1 text-sm text-slate-600">Of new members who opened a lesson, how many came back to the site on a later day?</p>
            <dl class="mt-4 grid grid-cols-2 gap-3">
                @foreach($returnToLearning as $window => ['returned' => $returned, 'eligible' => $eligible])
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <dt class="text-sm font-medium text-slate-700">{{ $window }}</dt>
                        <dd class="mt-1 text-2xl font-extrabold tabular-nums text-[#1447a6]">
                            @if($eligible === 0)
                                <span class="text-base font-semibold text-slate-500">Not enough data</span>
                            @else
                                {{ number_format($returned) }} <span class="text-xs font-medium text-slate-600">of {{ number_format($eligible) }}</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
            <p class="mt-3 text-xs leading-5 text-slate-600">Each window only counts students whose first lesson was at least that many days ago.</p>
        </section>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-labelledby="top-lessons-heading">
        <h3 id="top-lessons-heading" class="text-lg font-bold text-slate-900">Top 10 lessons</h3>
        <p class="mt-1 text-sm text-slate-600">Ranked by students who opened each lesson for the first time in this period. Repeat opens by the same student aren't counted.</p>
        @if($topLessons->isEmpty())
            <p class="mt-4 rounded-lg bg-slate-50 px-3 py-6 text-center text-sm text-slate-600">No lessons were opened in this period.</p>
        @else
            <div class="mt-3 overflow-x-auto">
                <table class="w-full min-w-[22rem] text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-700">
                        <tr>
                            <th scope="col" class="px-3 py-2">Lesson</th>
                            <th scope="col" class="px-3 py-2">Course area</th>
                            <th scope="col" class="px-3 py-2 text-right">Students</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($topLessons as $lesson)
                            <tr>
                                <th scope="row" class="px-3 py-2.5 font-semibold text-slate-800">{{ $lesson->title }}</th>
                                <td class="px-3 py-2.5 text-slate-600">{{ $lesson->area }}</td>
                                <td class="px-3 py-2.5 text-right font-bold tabular-nums text-[#1447a6]">{{ number_format($lesson->students) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <a href="{{ route('admin.lesson-analytics') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">View all lesson analytics <span aria-hidden="true" class="ml-2">→</span></a>
</section>
