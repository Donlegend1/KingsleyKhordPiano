@extends('layouts.admin')

@section('content')
@php
    // Literal class names so Tailwind picks them up.
    $badges = [
        'lesson' => 'bg-blue-50 text-blue-700',
        'lesson_reply' => 'bg-sky-50 text-sky-700',
        'forum' => 'bg-violet-50 text-violet-700',
        'forum_reply' => 'bg-fuchsia-50 text-fuchsia-700',
        'tutorial' => 'bg-amber-50 text-amber-700',
        'guide' => 'bg-emerald-50 text-emerald-700',
    ];
@endphp
<div class="p-4 sm:p-6 max-w-full">
    <nav class="mb-4 text-sm text-gray-500">Admin / <span class="text-blue-600">Comments</span></nav>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3">
            <h2 class="text-base font-bold text-gray-900">Comments</h2>
            <p class="text-xs text-gray-500">{{ number_format($grandTotal) }} {{ Str::plural('comment', $grandTotal) }} across lessons, community posts, tutorials and guides. Deleting a comment also removes its replies.</p>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-3 px-4 py-3">
            <div class="relative w-full sm:w-72">
                <span class="fa fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></span>
                <input type="search" name="q" value="{{ $search }}" placeholder="Search comment text or member"
                       class="w-full rounded-lg border border-gray-300 py-2 pl-8 pr-3 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <select name="source" aria-label="Comment type" onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <option value="all">All types ({{ $grandTotal }})</option>
                @foreach($sources as $key => [$label])
                    <option value="{{ $key }}" @selected($source === $key)>{{ $label }} ({{ $counts[$key] ?? 0 }})</option>
                @endforeach
            </select>
            @if($search !== '' || $source !== 'all')
                <a href="{{ route('admin.comments.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-800">Clear</a>
            @endif
        </form>

        @if($comments->isEmpty())
            <p class="px-4 py-10 text-center text-sm text-gray-500">No comments found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-y border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold">Member</th>
                            <th class="px-4 py-2.5 font-semibold">Comment</th>
                            <th class="px-4 py-2.5 font-semibold">Where</th>
                            <th class="px-4 py-2.5 font-semibold">Posted</th>
                            <th class="px-4 py-2.5 text-right font-semibold"><span class="sr-only">Delete</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($comments as $c)
                            @php($isReply = in_array($c->source, ['lesson_reply', 'forum_reply']))
                            <tr class="align-top hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900">{{ $c->member }}</p>
                                    <p class="max-w-[12rem] truncate text-xs text-gray-500">{{ $c->email }}</p>
                                </td>
                                <td class="max-w-md px-4 py-3">
                                    <p class="whitespace-pre-line break-words text-gray-800" title="{{ $c->body }}">{{ Str::limit($c->body, 220) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $badges[$c->source] }}">{{ $sources[$c->source][0] }}</span>
                                    <p class="mt-1 max-w-[15rem] truncate text-xs text-gray-500" title="{{ $c->where }}">
                                        @if($c->link)
                                            <a href="{{ $c->link }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">{{ $c->where }} <span class="fa fa-arrow-up-right-from-square text-[9px]"></span></a>
                                        @else
                                            {{ $c->where }}
                                        @endif
                                    </p>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-500">
                                    {{ $c->created->format('M j, Y') }}<br>{{ $c->created->diffForHumans() }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.comments.destroy', [$c->source, $c->id]) }}"
                                          onsubmit="return confirm('{{ $isReply || in_array($c->source, ['tutorial']) ? 'Delete this comment?' : 'Delete this comment and all replies to it?' }} This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Delete comment by {{ $c->member }}"
                                                class="rounded-md bg-red-50 px-2.5 py-1.5 text-red-600 hover:bg-red-100">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($comments->hasPages())
                <div class="border-t border-gray-100 px-4 py-3">{{ $comments->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection
