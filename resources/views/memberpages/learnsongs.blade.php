@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center min-h-8 gap-4">
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <a href="/home" class="hover:text-gray-700">Dashboard</a>
      <span>/</span>
      <span class="text-[#6366F1] font-medium">Learn Songs</span>
    </div>
    <div class="w-full sm:w-auto">
      <form method="GET" action="{{ route('learn.songs') }}" class="flex">
        <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
        <div class="relative w-full sm:w-72">
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="w-full h-8 border border-gray-200 rounded-full pl-4 pr-10 text-sm leading-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
            placeholder="Search songs..."
          >
          <button type="submit" class="absolute inset-y-0 right-3 flex items-center justify-center text-gray-400 hover:text-gray-600">
            <i class="fa fa-search text-sm"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</section>

@php
  $beginnerGroups     = $beginnerCategories->filter(fn($cat) => $cat->songs->isNotEmpty());
  $intermediateGroups = $intermediateCategories->filter(fn($cat) => $cat->songs->isNotEmpty());
  $advancedGroups     = $advancedCategories->filter(fn($cat) => $cat->songs->isNotEmpty());
  $allGroups          = $beginnerGroups->merge($intermediateGroups)->merge($advancedGroups);

  $levelLabels = [
    'beginner'     => 'Beginner',
    'intermediate' => 'Intermediate',
    'advanced'     => 'Advanced',
  ];
@endphp

<section class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
  <div class="w-full max-w-6xl mx-auto">

    @php
      $tabs = [
        'all'          => 'ALL',
        'beginner'     => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced'     => 'Advanced',
      ];
      $activeTabKey = request('tab', 'all');
      $tabQuery = fn($key) => '?tab=' . $key . (request('search') ? '&search=' . urlencode(request('search')) : '');
    @endphp

    <!-- Choose Tab -->
    <div class="mb-12">
      <!-- Mobile: Dropdown -->
      <div class="sm:hidden relative" x-data="{ open: false }" @click.outside="open = false">
          <button
              type="button"
              @click="open = !open"
              class="w-full flex items-center justify-between px-6 py-2.5 rounded-full font-semibold bg-indigo-600 text-white shadow-md shadow-indigo-600/20 transition-all duration-300"
          >
              <span>{{ $tabs[$activeTabKey] }}</span>
              <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
          </button>

          <div
              x-show="open"
              x-transition
              x-cloak
              class="absolute left-0 right-0 mt-2 rounded-2xl bg-white border border-gray-100 shadow-xl overflow-hidden z-20"
          >
              @foreach ($tabs as $key => $label)
                  <a href="{{ $tabQuery($key) }}"
                      class="block px-6 py-3 font-semibold transition-colors duration-150
                 {{ $activeTabKey === $key
                     ? 'bg-indigo-600 text-white'
                     : 'text-gray-600 hover:bg-gray-50' }}">
                      {{ $label }}
                  </a>
              @endforeach
          </div>
      </div>

      <!-- Desktop: Pills -->
      <div class="hidden sm:flex flex-wrap items-center gap-6">
          @foreach ($tabs as $key => $label)
              <a href="{{ $tabQuery($key) }}"
                  class="px-6 py-2.5 rounded-full font-semibold transition-all duration-300
             {{ $activeTabKey === $key
                 ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                 : 'text-gray-500 hover:text-gray-700' }}">
                  {{ $label }}
              </a>
          @endforeach
      </div>
    </div>

    <!-- Song Cards -->
    @php
      $activeTab = request('tab', 'all');

      $groups = match($activeTab) {
        'beginner'     => $beginnerGroups,
        'intermediate' => $intermediateGroups,
        'advanced'     => $advancedGroups,
        default        => $allGroups,
      };

      $levelMap = [];
      foreach($beginnerCategories as $cat)     $levelMap[$cat->id] = 'beginner';
      foreach($intermediateCategories as $cat) $levelMap[$cat->id] = 'intermediate';
      foreach($advancedCategories as $cat)     $levelMap[$cat->id] = 'advanced';
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($groups as $cat)
        @php
          $firstSong   = $cat->songs->first();
          $songCount   = $cat->songs->count();
          $level       = $levelMap[$cat->id] ?? 'beginner';
          $levelLabel  = $levelLabels[$level];
          $isNew       = !\App\Models\LessonView::hasViewed(auth()->id(), $firstSong)
              && $cat->songs->contains(fn ($s) => $s->created_at && $s->created_at->gt(now()->subDays(7)));
        @endphp

        <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col group">

          <!-- Thumbnail -->
          <a href="/member/lesson/{{ $firstSong->id }}" class="block relative overflow-hidden" style="aspect-ratio:16/9;">
            @if($firstSong->thumbnail_url)
              <img src="{{ $firstSong->thumbnail_url }}" alt="{{ $cat->category }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
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

            <!-- Song count badge -->
            <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md">
              {{ $songCount }} {{ Str::plural('Song', $songCount) }}
            </div>

            <!-- Play overlay -->
            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
              <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl scale-90 group-hover:scale-100 transition duration-300">
                <i class="fa fa-play text-indigo-600 text-sm ml-0.5"></i>
              </div>
            </div>
          </a>

          <!-- Card Body -->
          <div class="p-5 flex flex-col gap-3 flex-1">
            <h3 class="text-[15px] font-bold text-gray-900 dark:text-white leading-snug">
              {{ $cat->category }}
            </h3>

            <!-- Level Badge -->
            <div>
              <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-full border border-indigo-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ $levelLabel }}
              </span>
            </div>

            <!-- Watch Now Button -->
            <a href="/member/lesson/{{ $firstSong->id }}"
               class="mt-auto flex items-center justify-center w-full py-3 bg-[#6366F1] hover:bg-[#4F46E5] text-white text-sm font-bold rounded-xl transition-all duration-200">
              Watch Now
            </a>
          </div>
        </div>

      @empty
        <div class="col-span-full text-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100">
          <i class="fa fa-music text-5xl text-gray-200 mb-4 block"></i>
          <p class="text-gray-500 font-medium">No songs found for this level.</p>
        </div>
      @endforelse
    </div>

  </div>
</section>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

    .font-sans {
        font-family: 'Outfit', sans-serif;
    }
</style>
@endsection
