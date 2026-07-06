@extends('layouts.member')

@section('content')

<div x-data="{ search: '' }">

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm text-gray-500 min-h-8">
    <div class="flex items-center gap-2">
      <a href="/home" class="hover:text-gray-700">Dashboard</a>
      <span>/</span>
      <span class="text-blue-600 font-medium">Learn Songs</span>
    </div>

    <!-- Search Bar -->
    <div class="relative w-full sm:w-72 group">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/>
        </svg>
        <input
            type="text"
            x-model="search"
            placeholder="Search songs..."
            class="w-full h-10 pl-10 pr-9 rounded-xl border-0 bg-gray-100 dark:bg-white/5 text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 outline-none ring-1 ring-transparent focus:bg-white dark:focus:bg-[#161617] focus:ring-2 focus:ring-indigo-500/40 transition-all"
        >
        <button type="button" x-show="search !== ''" x-cloak @click="search = ''"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>
  </div>
</section>

@php
  $levelLabels = [
    'beginner'     => 'Beginner',
    'intermediate' => 'Intermediate',
    'advanced'     => 'Advanced',
  ];

  $buildQuery = fn($overrides) => '?' . http_build_query(array_merge([
      'tab' => $activeTab,
      'key' => $tonalCenter,
  ], $overrides));
@endphp

<section class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
  <div class="w-full max-w-6xl mx-auto">

    @php
      $skillOptions = ['all' => 'All'] + $levelLabels;
      $keyOptions = ['all' => 'All Keys'] + $tonalCenters;
    @endphp

    <!-- Filters -->
    <div class="mb-10 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-6 relative z-30">

      <!-- Skill Level dropdown -->
      <div class="flex-1" x-data="{ open: false }" @click.outside="open = false">
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Skill Level</label>
        <div class="relative">
          <button type="button" @click="open = !open"
            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 border-2 border-blue-200 dark:border-blue-900 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition">
            <span>{{ $skillOptions[$activeTab] ?? 'All' }}</span>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <div x-show="open" x-transition x-cloak
            class="absolute left-0 right-0 mt-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden z-40">
            @foreach ($skillOptions as $key => $label)
              <a href="{{ $buildQuery(['tab' => $key]) }}"
                class="flex items-center justify-between px-4 py-2.5 text-sm font-medium transition-colors duration-150
                  {{ $activeTab === $key
                      ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 font-semibold'
                      : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                {{ $label }}
                @if($activeTab === $key)
                  <i class="fa fa-check text-blue-600 dark:text-blue-300 text-xs"></i>
                @endif
              </a>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Tonal Center dropdown -->
      <div class="flex-1" x-data="{ open: false }" @click.outside="open = false">
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tonal Center (Key)</label>
        <div class="relative">
          <button type="button" @click="open = !open"
            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 border-2 border-blue-200 dark:border-blue-900 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition">
            <span>{{ $keyOptions[$tonalCenter] ?? 'All Keys' }}</span>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <div x-show="open" x-transition x-cloak
            class="absolute left-0 right-0 mt-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden z-40 p-3">
            <a href="{{ $buildQuery(['key' => 'all']) }}"
              class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 mb-2
                {{ $tonalCenter === 'all'
                    ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 font-semibold'
                    : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
              All Keys
              @if($tonalCenter === 'all')
                <i class="fa fa-check text-blue-600 dark:text-blue-300 text-xs"></i>
              @endif
            </a>
            <div class="grid grid-cols-3 gap-1.5 border-t border-gray-100 dark:border-gray-700 pt-2">
              @foreach ($tonalCenters as $value => $label)
                <a href="{{ $buildQuery(['key' => $value]) }}"
                  class="flex items-center justify-center gap-1 px-2 py-2 rounded-lg text-xs font-medium text-center whitespace-nowrap transition-colors duration-150
                    {{ $tonalCenter === $value
                        ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 font-semibold'
                        : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                  {{ $label }}
                  @if($tonalCenter === $value)
                    <i class="fa fa-check text-blue-600 dark:text-blue-300 text-[10px]"></i>
                  @endif
                </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Song Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($songs as $song)
        @php
          $levelLabel  = $levelLabels[$song->level] ?? ucfirst($song->level);
          $isNew       = \App\Models\LessonView::anyNewUnviewed(auth()->id(), collect([$song]));
          $searchableText = Str::lower($song->title . ' ' . ($song->category->category ?? ''));
        @endphp

        <div
          x-show="search === '' || {{ \Illuminate\Support\Js::from($searchableText) }}.includes(search.toLowerCase())"
          class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col group">

          <!-- Thumbnail -->
          <a href="/member/lesson/{{ $song->id }}?type=learn_song" class="block relative overflow-hidden" style="aspect-ratio:16/9;">
            @if($song->thumbnail_url)
              <img src="{{ $song->thumbnail_url }}" alt="{{ $song->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @else
              <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-900 to-purple-900">
                <i class="fa fa-music text-5xl text-white/30"></i>
              </div>
            @endif

            @if($isNew)
              <div class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-md tracking-wide">
                NEW
              </div>
            @endif

            <!-- Play overlay -->
            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
              <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl scale-90 group-hover:scale-100 transition duration-300">
                <i class="fa fa-play text-indigo-600 text-sm ml-0.5"></i>
              </div>
            </div>
          </a>

          <!-- Card Body -->
          <div class="p-5 flex flex-col gap-2 flex-1">
            <h3 class="text-[15px] font-bold text-gray-900 dark:text-white leading-snug">
              {{ $song->title }}
            </h3>

            @if($song->category?->category)
              <p class="text-xs text-gray-400 dark:text-gray-500 -mt-1">{{ $song->category->category }}</p>
            @endif

            <!-- Level Badge -->
            <div>
              <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-full border border-indigo-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ $levelLabel }}
              </span>
            </div>

            <!-- Watch Now Button -->
            <a href="/member/lesson/{{ $song->id }}?type=learn_song"
               class="mt-auto flex items-center justify-center w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all duration-200">
              Watch Now
            </a>
          </div>
        </div>

      @empty
        <div class="col-span-full text-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100">
          <i class="fa fa-music text-5xl text-gray-200 mb-4 block"></i>
          <p class="text-gray-500 font-medium">No songs found for this filter.</p>
        </div>
      @endforelse
    </div>

    @if ($songs->hasPages())
      <div class="flex justify-center py-8">
        {{ $songs->links('components.pagination') }}
      </div>
    @endif

  </div>
</section>

</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

    .font-sans {
        font-family: 'Outfit', sans-serif;
    }
</style>
@endsection
