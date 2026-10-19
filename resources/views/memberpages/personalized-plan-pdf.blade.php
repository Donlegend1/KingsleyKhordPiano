<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Personalized Plan - {{ $studentName }}</title>
<style>
    @page { margin: 36px 40px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }
    h1 { font-size: 20px; margin: 0 0 2px 0; color: #111827; }
    h2 { font-size: 14px; margin: 0 0 4px 0; color: #111827; }
    h3 { font-size: 10px; margin: 14px 0 6px 0; color: #1447A6; text-transform: uppercase; letter-spacing: 0.5px; }
    .muted { color: #6b7280; }
    .brand { font-size: 9px; letter-spacing: 1.5px; text-transform: uppercase; color: #C85A5A; margin-bottom: 8px; }
    .summary { width: 100%; border-collapse: collapse; margin: 14px 0 6px 0; }
    .summary td { width: 33%; padding: 10px 12px; border: 1px solid #e5e7eb; vertical-align: top; }
    .label { font-size: 9px; color: #9ca3af; text-transform: uppercase; }
    .value { font-size: 12px; font-weight: bold; color: #111827; margin-top: 2px; }
    .box { border: 1px solid #e5e7eb; padding: 10px 14px; margin-top: 12px; }
    .month { margin-top: 22px; page-break-inside: auto; }
    .month-head { border-bottom: 2px solid #1447A6; padding-bottom: 4px; margin-bottom: 4px; }
    table.lessons { width: 100%; border-collapse: collapse; }
    table.lessons td { padding: 5px 4px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
    td.check { width: 16px; font-size: 13px; }
    td.meta { width: 110px; text-align: right; color: #6b7280; font-size: 10px; }
    a { color: #1447A6; text-decoration: underline; }
    .done a { color: #9ca3af; text-decoration: line-through; }
    .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
@php
    $categoryLabels = [
        'finger_exercise' => 'Finger Exercise',
        'theory_and_application' => 'Theory and Application',
        'guided_practice' => 'Guided Practice',
        'repertoire' => 'Repertoire',
    ];
    $completed = $plan->completed_lessons ?? [];
    $endDate = $plan->end_date;

    // A PDF link must be a full web address. Full links are used as saved;
    // site links like /member/lesson/1 are made absolute; anything else
    // (blank, or not a usable address) is shown as plain text.
    $lessonLink = function ($raw) {
        $raw = trim((string) $raw);

        if (preg_match('#^https?://#i', $raw)) {
            return $raw;
        }

        return str_starts_with($raw, '/') ? url($raw) : null;
    };
@endphp

<div class="brand">Kingsley Khord Piano</div>
<h1>My Personalized Plan</h1>
<div class="muted">
    90-day program
    @if ($plan->start_date)
        &middot; {{ $plan->start_date->format('M j, Y') }} &ndash; {{ $endDate->format('M j, Y') }}
    @endif
</div>

<table class="summary">
    <tr>
        <td>
            <div class="label">Student</div>
            <div class="value">{{ $studentName }}</div>
            @if ($plan->skill_level)
                <div class="muted">{{ $plan->skill_level }}</div>
            @endif
        </td>
        <td>
            <div class="label">Goal</div>
            <div class="value">{{ $plan->goal ?: 'Not set yet' }}</div>
        </td>
        <td>
            <div class="label">Overall progress</div>
            <div class="value">{{ $plan->progressPercent() }}%</div>
        </td>
    </tr>
</table>

@if (!empty($plan->ninety_day_target))
    <div class="box">
        <h2>90-Day Target</h2>
        @foreach ($plan->ninety_day_target as $target)
            <div>&#9679; {{ $target }}</div>
        @endforeach
    </div>
@endif

@foreach ($plan->months ?? [] as $index => $month)
    @php $range = $plan->monthDateRange($index); @endphp
    <div class="month">
        <div class="month-head">
            <h2>Month {{ $index + 1 }}</h2>
            <span class="muted">
                @if ($range) {{ $range[0]->format('M j') }} &ndash; {{ $range[1]->format('M j') }} &middot; @endif
                {{ $plan->monthProgressPercent($index) }}% complete
            </span>
        </div>

        @php $hasLessons = false; @endphp
        @foreach ($categoryLabels as $key => $label)
            @php $lessons = $month['lessons'][$key] ?? []; @endphp
            @if (!empty($lessons))
                @php $hasLessons = true; @endphp
                <h3>{{ $label }}</h3>
                <table class="lessons">
                    @foreach ($lessons as $lesson)
                        @php $isDone = in_array("{$index}.{$key}.{$lesson['id']}", $completed, true); @endphp
                        <tr class="{{ $isDone ? 'done' : '' }}">
                            <td class="check">{{ $isDone ? '☑' : '☐' }}</td>
                            <td>
                                @if ($href = $lessonLink($lesson['url'] ?? ''))
                                    <a href="{{ $href }}">{{ $lesson['name'] ?: 'Untitled lesson' }}</a>
                                @else
                                    {{ $lesson['name'] ?: 'Untitled lesson' }}
                                @endif
                            </td>
                            <td class="meta">
                                @if (!empty($lesson['week'])) Week {{ $lesson['week'] }} @endif
                                @if (!empty($lesson['week']) && !empty($lesson['duration'])) &middot; @endif
                                @if (!empty($lesson['duration'])) {{ $lesson['duration'] }} min @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        @endforeach

        @unless ($hasLessons)
            <p class="muted">No lessons yet.</p>
        @endunless
    </div>
@endforeach

<div class="footer">Kingsley Khord Piano &middot; Generated {{ now()->format('M j, Y') }}</div>
</body>
</html>
