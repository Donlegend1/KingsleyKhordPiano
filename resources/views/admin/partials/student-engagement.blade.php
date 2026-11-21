@php
    $firstWeek = [
        ['Created an account', 120],
        ['Took the assessment', 88],
        ['Started a first lesson', 73],
        ['Completed a first lesson', 52],
    ];
    $returningStudents = [
        ['Within 7 days', 58],
        ['Within 30 days', 76],
    ];
@endphp

<section aria-labelledby="engagement-heading" class="space-y-4">
    <header class="flex flex-wrap items-start justify-between gap-2">
        <div>
            <h2 id="engagement-heading" class="text-xl font-bold text-slate-900">Student Engagement</h2>
            <p class="mt-1 text-sm text-slate-600">Illustrative reports using a fixed demo dataset.</p>
        </div>
        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">Demo data</span>
    </header>

    <div class="grid gap-4 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <h3 class="text-lg font-bold text-slate-900">First-week progress</h3>
            <p class="mt-1 text-sm text-slate-600">What 120 new members did within seven days of joining.</p>
            <dl class="mt-4 space-y-3">
                @foreach($firstWeek as [$action, $count])
                    @php
                        $rate = (int) round($count / 120 * 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <dt>{{ $action }}</dt>
                            <dd class="shrink-0 font-semibold tabular-nums text-slate-900">{{ $count }} <span class="ml-1 text-xs font-normal text-slate-600">({{ $rate }}%)</span></dd>
                        </div>
                        <div class="mt-1.5 h-1.5 rounded-full bg-slate-100" aria-hidden="true">
                            <div class="h-full rounded-full bg-blue-600" style="width: {{ $rate }}%"></div>
                        </div>
                    </div>
                @endforeach
            </dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
            <h3 class="text-lg font-bold text-slate-900">Return to learning</h3>
            <p class="mt-1 text-sm text-slate-600">Of 100 students who started learning, how many came back for a lesson or practice activity on a later day?</p>
            <dl class="mt-4 grid grid-cols-2 gap-3">
                @foreach($returningStudents as [$window, $count])
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                        <dt class="text-sm font-medium text-slate-700">{{ $window }}</dt>
                        <dd class="mt-1 text-2xl font-extrabold tabular-nums text-[#1447a6]">{{ $count }} <span class="text-xs font-medium text-slate-600">of 100</span></dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
        <h3 class="text-lg font-bold text-slate-900">Top 10 most-viewed member lessons</h3>
        <p class="mt-1 text-sm text-slate-600">Sample ranking by lesson opens. Repeat opens count toward total views.</p>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full min-w-[22rem] text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-700">
                    <tr>
                        <th scope="col" class="px-3 py-2">Lesson</th>
                        <th scope="col" class="px-3 py-2">Course area</th>
                        <th scope="col" class="px-3 py-2 text-right">Views</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach([
                        ['Find the Notes on the Keyboard', 'Roadmap', 482],
                        ['Left and Right Hand Independence', 'Finger Exercises', 395],
                        ['Amazing Grace · Chords and MIDI', 'Learn Songs', 341],
                        ['Practise a Gospel 2–5–1', 'Guided Practice', 298],
                        ['Introduction to Chord Extensions', 'Extra Courses', 247],
                        ['C Major Study', 'Etudes & Pieces', 196],
                        ['Build Your First Triads', 'Roadmap', 184],
                        ['Play Chord Changes in Time', 'Guided Practice', 167],
                        ['Even Touch Across Five Fingers', 'Finger Exercises', 151],
                        ['Jesus Loves Me · Chords and MIDI', 'Learn Songs', 138],
                    ] as [$lesson, $area, $views])
                        <tr>
                            <th scope="row" class="px-3 py-2.5 font-semibold text-slate-800">{{ $lesson }}</th>
                            <td class="px-3 py-2.5 text-slate-600">{{ $area }}</td>
                            <td class="px-3 py-2.5 text-right font-bold tabular-nums text-[#1447a6]">{{ number_format($views) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <a href="{{ route('admin.lesson-analytics') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-blue-800 hover:bg-blue-50">View all lesson analytics <span aria-hidden="true" class="ml-2">→</span></a>
</section>
