@extends('layouts.admin')

@section('content')

<main class="flex-1 p-6 bg-gray-50 min-h-screen">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Audio Quiz</h2>
        <p class="text-[13px] text-gray-400 mt-1">Choose a category to manage its lessons and questions.</p>
    </header>

    @if (session('success'))
        <div class="max-w-5xl mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-[13px] font-semibold text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-5xl">
        @foreach ($categories as $cat)
            @php
                $percent = $cat['lesson_count'] ? round(($cat['configured_count'] / $cat['lesson_count']) * 100) : 0;
            @endphp

            <a href="{{ route('admin.audio-quiz', ['category' => $cat['db_category']]) }}"
                class="block bg-white border border-gray-100 rounded-2xl p-5 hover:border-gray-300 hover:shadow-sm transition-all">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <h3 class="text-[14px] font-bold text-gray-900 leading-snug">{{ $cat['name'] }}</h3>
                    @if (!$cat['is_built'])
                        <span class="flex-shrink-0 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-600 text-[10px] font-bold uppercase tracking-wide">
                            Coming soon
                        </span>
                    @endif
                </div>

                <p class="text-[12px] text-gray-400 mb-3">
                    {{ $cat['lesson_count'] }} {{ Str::plural('lesson', $cat['lesson_count']) }}
                    &middot;
                    {{ $cat['configured_count'] }}/{{ $cat['lesson_count'] }} configured
                </p>

                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gray-900 rounded-full" style="width: {{ $percent }}%"></div>
                </div>
            </a>
        @endforeach
    </div>
</main>

@endsection
