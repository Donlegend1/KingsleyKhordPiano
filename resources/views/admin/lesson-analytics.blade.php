@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-[1440px] space-y-5 px-4 py-6 sm:px-7 sm:py-8">
    <header class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Lesson Analytics</h1>
            <p class="mt-1 text-sm text-slate-600">Compare activity across the uploaded member lesson catalog.</p>
        </div>
        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800">Demo activity counts</span>
    </header>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5" aria-label="Lesson analytics report">
        <form method="GET" action="{{ route('admin.lesson-analytics') }}" class="flex flex-wrap items-end gap-3">
            <label class="min-w-0 grow text-xs font-semibold text-slate-700">Lesson title
                <input name="q" value="{{ $search }}" type="search" maxlength="150" placeholder="Search lessons" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </label>
            <label class="text-xs font-semibold text-slate-700">Course area
                <select name="area" class="mt-1 block rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                    <option value="">All areas</option>
                    @foreach(array_keys($areas) as $option)
                        <option value="{{ $option }}" @selected($area === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-xs font-semibold text-slate-700">Sort by
                <select name="sort" class="mt-1 block rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                    @foreach($sortOptions as $key => $label)
                        <option value="{{ $key }}" @selected($sort === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="rounded-lg bg-[#1447a6] px-4 py-2 text-sm font-semibold text-white">Apply</button>
        </form>

        <dl class="my-4 grid gap-2 rounded-xl bg-slate-50 p-3 text-xs text-slate-600 sm:grid-cols-2 lg:grid-cols-4">
            <div><dt class="font-bold text-slate-900">Started</dt><dd>Selected the lesson or used a start or restart button.</dd></div>
            <div><dt class="font-bold text-slate-900">Completed video</dt><dd>Watched the video through to the end.</dd></div>
            <div><dt class="font-bold text-slate-900">Returned</dt><dd>Opened the same lesson again, whether or not they completed it.</dd></div>
            <div><dt class="font-bold text-slate-900">Continued</dt><dd>Opened the next lesson or a related lesson from this one.</dd></div>
        </dl>

        <p class="mb-2 text-xs text-slate-600">{{ number_format($lessons->total()) }} {{ Str::plural('lesson', $lessons->total()) }} · Unique students per activity in each row</p>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[37rem] text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-700">
                    <tr>
                        <th scope="col" class="px-3 py-2">Lesson</th>
                        <th scope="col" class="px-3 py-2">Course area</th>
                        <th scope="col" class="px-3 py-2 text-right">Started</th>
                        <th scope="col" class="px-3 py-2 text-right">Completed video</th>
                        <th scope="col" class="px-3 py-2 text-right">Returned</th>
                        <th scope="col" class="px-3 py-2 text-right">Continued</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lessons as $lesson)
                        <tr>
                            <th scope="row" class="px-3 py-2.5 font-semibold text-slate-900">{{ $lesson->title }}</th>
                            <td class="px-3 py-2.5 text-slate-600">
                                {{ $lesson->area }}
                                @if($lesson->skill_level)
                                    <span class="mt-0.5 block text-xs text-slate-500">{{ str($lesson->skill_level)->lower()->title() }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-right tabular-nums">{{ number_format($lesson->starts) }}</td>
                            <td class="px-3 py-2.5 text-right tabular-nums">{{ number_format($lesson->completions) }}</td>
                            <td class="px-3 py-2.5 text-right tabular-nums">{{ number_format($lesson->returned) }}</td>
                            <td class="px-3 py-2.5 text-right tabular-nums">{{ number_format($lesson->continued) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-5 text-slate-600">No lessons match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lessons->hasPages())
            <div class="mt-4">{{ $lessons->links('components.pagination') }}</div>
        @endif
    </section>
</div>
@endsection
