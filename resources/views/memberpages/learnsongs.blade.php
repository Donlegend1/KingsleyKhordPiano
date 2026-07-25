@extends('layouts.member')

@section('content')
@php
  $selectedLevels = collect($levelFilter ?? []);
  $selectedKeys = collect($keyFilter ?? []);
  $keyOptions = collect($keys ?? [])->map(fn ($label, $value) => [
      'value' => $value,
      'label' => $label,
  ])->values();
@endphp

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-5 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex flex-col gap-3">
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <a href="/home" class="hover:text-gray-700">Dashboard</a>
      <span>/</span>
      <span class="text-blue-600 font-medium">Learn Songs</span>
    </div>
    <div class="w-full">
      <form method="GET" action="{{ route('learn.songs') }}" class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50/80 p-3 shadow-sm lg:flex-row lg:items-center dark:border-gray-800 dark:bg-gray-900">
        <div class="relative min-w-0 flex-1">
          <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <i class="fa fa-search text-sm"></i>
          </span>
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="h-11 w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100"
            placeholder="Search songs..."
          >
        </div>
        <div
          class="relative lg:w-64"
          x-data="{
            open: false,
            search: '',
            options: @js($levels),
            selected: @js($selectedLevels->values()->all()),
            filteredOptions() {
              return this.options.filter((option) => option.toLowerCase().includes(this.search.toLowerCase()));
            },
            toggleLevel(level) {
              if (level === 'All') {
                this.selected = ['All'];
                return;
              }

              const next = this.selected.includes(level)
                ? this.selected.filter((selectedLevel) => selectedLevel !== level)
                : [...this.selected.filter((selectedLevel) => selectedLevel !== 'All'), level];

              this.selected = next.length ? next : [];
            },
            label() {
              const selectedLevels = this.selected.filter((level) => level !== 'All');
              if (!selectedLevels.length) return 'All levels';
              if (selectedLevels.length <= 2) return selectedLevels.join(', ');
              return `${selectedLevels.length} levels selected`;
            }
          }"
          @click.outside="open = false"
        >
          <template x-for="level in selected" :key="level">
            <input type="hidden" name="level[]" :value="level">
          </template>
          <button type="button" @click="open = !open" class="flex h-11 w-full items-center justify-between gap-3 rounded-lg border border-slate-300 bg-white px-3 text-left text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100">
            <span class="truncate" x-text="label()"></span>
            <i class="fa fa-chevron-down text-xs text-slate-400"></i>
          </button>
          <div x-cloak x-show="open" x-transition class="absolute right-0 z-30 mt-2 w-full rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
            <div class="relative mb-2">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="fa fa-search text-xs"></i>
              </span>
              <input type="text" x-model="search" placeholder="Find level..." class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 pl-8 pr-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </div>
            <div class="max-h-56 space-y-1 overflow-y-auto">
              <template x-for="level in filteredOptions()" :key="level">
                <label class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50">
                  <span x-text="level"></span>
                  <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" :checked="selected.includes(level)" @change="toggleLevel(level)">
                </label>
              </template>
              <p x-show="filteredOptions().length === 0" class="px-3 py-2 text-sm text-slate-500">No levels found.</p>
            </div>
          </div>
        </div>
        <div
          class="relative lg:w-64"
          x-data="{
            open: false,
            search: '',
            keys: @js($keyOptions),
            selected: @js($selectedKeys->values()->all()),
            filteredKeys() {
              return this.keys.filter((key) => {
                const query = this.search.toLowerCase();
                return key.label.toLowerCase().includes(query) || key.value.toLowerCase().includes(query);
              });
            },
            toggleKey(value) {
              this.selected = this.selected.includes(value)
                ? this.selected.filter((selectedKey) => selectedKey !== value)
                : [...this.selected, value];
            },
            label() {
              if (!this.selected.length) return 'All keys';
              const labels = this.selected.map((value) => {
                const match = this.keys.find((key) => key.value === value);
                return match ? match.label : value;
              });
              if (labels.length <= 2) return labels.join(', ');
              return `${labels.length} keys selected`;
            }
          }"
          @click.outside="open = false"
        >
          <template x-for="key in selected" :key="key">
            <input type="hidden" name="key[]" :value="key">
          </template>
          <button type="button" @click="open = !open" class="flex h-11 w-full items-center justify-between gap-3 rounded-lg border border-slate-300 bg-white px-3 text-left text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-100">
            <span class="truncate" x-text="label()"></span>
            <i class="fa fa-chevron-down text-xs text-slate-400"></i>
          </button>
          <div x-cloak x-show="open" x-transition class="absolute right-0 z-30 mt-2 w-full rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
            <div class="relative mb-2">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="fa fa-search text-xs"></i>
              </span>
              <input type="text" x-model="search" placeholder="Find key..." class="h-9 w-full rounded-lg border border-slate-200 bg-slate-50 pl-8 pr-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </div>
            <div class="max-h-56 space-y-1 overflow-y-auto">
              <template x-for="key in filteredKeys()" :key="key.value">
                <label class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-indigo-50">
                  <span x-text="key.label"></span>
                  <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" :checked="selected.includes(key.value)" @change="toggleKey(key.value)">
                </label>
              </template>
              <p x-show="filteredKeys().length === 0" class="px-3 py-2 text-sm text-slate-500">No keys found.</p>
            </div>
          </div>
        </div>
        <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
          <i class="fa fa-sliders text-sm"></i>
          <span>Filter</span>
        </button>
        @if (request()->hasAny(['search', 'level', 'key']))
          <a href="{{ route('learn.songs') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">
            Clear
          </a>
        @endif
      </form>
    </div>
  </div>
</section>

@php
  $beginnerGroups = $beginnerCategories->filter(fn($cat) => $cat->songs->isNotEmpty());
  $intermediateGroups = $intermediateCategories->filter(fn($cat) => $cat->songs->isNotEmpty());
  $advancedGroups = $advancedCategories->filter(fn($cat) => $cat->songs->isNotEmpty());
  $allGroups = $beginnerGroups->merge($intermediateGroups)->merge($advancedGroups);

  $levelLabels = [
    'beginner' => 'Beginner',
    'intermediate' => 'Intermediate',
    'advanced' => 'Advanced',
  ];
@endphp

<section class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
  <div class="w-full max-w-7xl mx-auto">

    @php
      $groups = $allGroups;

      $levelMap = [];
      foreach($beginnerCategories as $cat) $levelMap[$cat->id] = 'beginner';
      foreach($intermediateCategories as $cat) $levelMap[$cat->id] = 'intermediate';
      foreach($advancedCategories as $cat) $levelMap[$cat->id] = 'advanced';
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($groups as $cat)
        @php
          $firstSong = $cat->songs->first();
          $songCount = $cat->songs->count();
          $level = $levelMap[$cat->id] ?? 'beginner';
          $levelLabel = $levelLabels[$level];
          $isNew = \App\Models\LessonView::anyNewUnviewed(auth()->id(), $cat->songs);
          $tonalCenterLabel = $firstSong->tonal_center ? ($keys[$firstSong->tonal_center] ?? $firstSong->tonal_center) : null;
        @endphp

        <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col group">

          <a href="/member/lesson/{{ $firstSong->id }}?type=learn_song" class="block relative overflow-hidden" style="aspect-ratio:16/9;">
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

            <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md">
              {{ $songCount }} {{ Str::plural('Song', $songCount) }}
            </div>

            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
              <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl scale-90 group-hover:scale-100 transition duration-300">
                <i class="fa fa-play text-indigo-600 text-sm ml-0.5"></i>
              </div>
            </div>
          </a>

          <div class="p-5 flex flex-col gap-3 flex-1">
            <h3 class="text-[15px] font-bold text-gray-900 dark:text-white leading-snug">
              {{ $cat->category }}
            </h3>

            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-full border border-indigo-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ $levelLabel }}
              </span>
              @if($tonalCenterLabel)
                <span class="inline-flex items-center gap-1.5 bg-slate-50 text-slate-600 text-xs font-semibold px-2.5 py-1 rounded-full border border-slate-200">
                  <i class="fa fa-music text-[10px]"></i>
                  Key: {{ $tonalCenterLabel }}
                </span>
              @endif
            </div>

            <a href="/member/lesson/{{ $firstSong->id }}?type=learn_song"
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

  </div>
</section>

@endsection
