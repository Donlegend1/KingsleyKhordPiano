@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center min-h-8 gap-4">
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <a href="/home" class="hover:text-gray-700">Dashboard</a>
      <span>/</span>
      <span class="text-[#6366F1] font-medium">Extra Courses</span>
    </div>
    <div class="w-full sm:w-auto">
      <form method="GET" action="{{ route('extra.courses') }}" class="flex">
        <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">
        <div class="relative w-full sm:w-72">
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="w-full h-8 border border-gray-200 rounded-full pl-4 pr-10 text-sm leading-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
            placeholder="Search courses..."
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
  $beginnerGroups    = $beginnerCategories->filter(fn($cat) => $cat->courses->isNotEmpty());
  $intermediateGroups = $intermediateCategories->filter(fn($cat) => $cat->courses->isNotEmpty());
  $advancedGroups    = $advancedCategories->filter(fn($cat) => $cat->courses->isNotEmpty());
  $allGroups         = $beginnerGroups->merge($intermediateGroups)->merge($advancedGroups);

  $levelLabels = [
    'beginner'     => 'Beginner',
    'intermediate' => 'Intermediate',
    'advanced'     => 'Advanced',
  ];
@endphp

<section class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
  <div class="w-full max-w-7xl mx-auto" x-data="{ activeTab: '{{ request('tab', 'all') }}' }">

    <!-- Mobile Dropdown -->
    <div class="block lg:hidden mb-6" x-data="{ open: false, selected: '{{ request('tab', 'all') }}',
      options: [
        { value: 'all',          label: 'All',          icon: '⊞' },
        { value: 'beginner',     label: 'Beginner',     icon: '▲' },
        { value: 'intermediate', label: 'Intermediate', icon: '↗' },
        { value: 'advanced',     label: 'Advanced',     icon: '★' },
      ],
      get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label ?? 'All'; },
      pick(val) { this.selected = val; this.open = false; window.location.href = '?tab=' + val + '{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}'; }
    }" @click.outside="open = false">

      <!-- Trigger -->
      <button @click="open = !open" type="button"
        class="w-full flex items-center justify-between rounded-2xl px-5 py-4 shadow-sm transition-all duration-200 bg-[#6366F1]">
        <p class="text-base font-bold text-white" x-text="selectedLabel"></p>
        <svg class="w-5 h-5 text-white transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>

      <!-- Dropdown Panel -->
      <div x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        style="display:none;"
        class="mt-2 w-full bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-50">
        <template x-for="opt in options" :key="opt.value">
          <button @click="pick(opt.value)" type="button"
            class="w-full flex items-center justify-between px-5 py-3.5 transition-colors duration-150 border-b border-gray-50 last:border-0"
            :class="selected === opt.value ? 'bg-[#6366F1]' : 'hover:bg-gray-50'">
            <span class="text-sm font-semibold" :class="selected === opt.value ? 'text-white' : 'text-gray-700'" x-text="opt.label"></span>
            <svg x-show="selected === opt.value" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
          </button>
        </template>
      </div>
    </div>

    <!-- Desktop Tabs -->
    <div class="hidden lg:flex flex-wrap items-center justify-center gap-6 mb-10">
      @php
        $tabs = [
          'all'          => 'ALL',
          'beginner'     => 'Beginner',
          'intermediate' => 'Intermediate',
          'advanced'     => 'Advanced',
        ];
      @endphp

      @foreach($tabs as $key => $label)
        <a href="?tab={{ $key }}{{ request('search') ? '&search=' . urlencode(request('search')) : '' }}"
           class="px-6 py-2.5 rounded-full font-semibold transition-all duration-300
             {{ request('tab', 'all') === $key
               ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
               : 'text-gray-500 hover:text-gray-700' }}">
          {{ $label }}
        </a>
      @endforeach
    </div>

    <!-- Course Cards -->
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
          $firstCourse = $cat->courses->first();
          $lessonCount = $cat->courses->count();
          $level       = $levelMap[$cat->id] ?? 'beginner';
          $levelLabel  = $levelLabels[$level];
        @endphp

        <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col group">

          <!-- Thumbnail -->
          <a href="/member/lesson/{{ $firstCourse->id }}" class="block relative overflow-hidden" style="aspect-ratio:16/9;">
            @if($firstCourse->thumbnail_url)
              <img src="{{ $firstCourse->thumbnail_url }}" alt="{{ $cat->category }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @else
              <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-900 to-purple-900">
                <i class="fa fa-music text-5xl text-white/30"></i>
              </div>
            @endif

            <!-- Lesson count badge -->
            <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md">
              {{ $lessonCount }} {{ Str::plural('Lesson', $lessonCount) }}
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
            <a href="/member/lesson/{{ $firstCourse->id }}"
               class="mt-auto flex items-center justify-center w-full py-3 bg-[#6366F1] hover:bg-[#4F46E5] text-white text-sm font-bold rounded-xl transition-all duration-200">
              Watch Now
            </a>
          </div>
        </div>

      @empty
        <div class="col-span-full text-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100">
          <i class="fa fa-graduation-cap text-5xl text-gray-200 mb-4 block"></i>
          <p class="text-gray-500 font-medium">No courses found for this level.</p>
        </div>
      @endforelse
    </div>

  </div>
</section>

@endsection
