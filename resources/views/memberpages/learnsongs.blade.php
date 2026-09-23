@extends('layouts.member')

@section('content')

<div
  x-data="learnSongsPage({{ \Illuminate\Support\Js::from($search ?? '') }}, {{ \Illuminate\Support\Js::from($activeTab) }}, {{ \Illuminate\Support\Js::from($tonalCenter) }})"
  x-init="init()"
>

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm text-gray-500 min-h-8">
    <div class="flex items-center gap-2">
      <a href="/home" class="hover:text-gray-700">Dashboard</a>
      <span>/</span>
      <span class="text-[#C85A5A] font-medium">Learn Songs</span>
    </div>

    <!-- Search Bar -->
    <div class="relative w-full sm:w-72 group">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/>
        </svg>
        <input
            type="text"
            x-model="search"
            x-on:input.debounce.400ms="onSearchInput()"
            placeholder="Search songs..."
            autocomplete="off"
            class="w-full h-10 pl-10 pr-9 rounded-xl border-0 bg-gray-100 dark:bg-white/5 text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 outline-none ring-1 ring-transparent focus:bg-white dark:focus:bg-[#161617] focus:ring-2 focus:ring-indigo-500/40 transition-all"
        >
        <button type="button" x-show="search !== ''" x-cloak @click="clearSearch()"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>
  </div>
</section>

@php
  $skillOptions = ['all' => 'All', 'beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'];
  $keyOptions = ['all' => 'All Keys'] + $tonalCenters;
@endphp

<section class="bg-gray-50 dark:bg-gray-950 min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans">
  <div class="w-full max-w-6xl mx-auto">

    <!-- Filters -->
    <div class="mb-10 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-6 relative z-30">

      <!-- Skill Level dropdown -->
      <div class="flex-1" x-data="{ open: false }" @click.outside="open = false">
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Skill Level</label>
        <div class="relative">
          <button type="button" @click="open = !open"
            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 border-2 border-blue-200 dark:border-blue-900 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition">
            <span x-text="skillOptions[activeTab] ?? 'All'"></span>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <div x-show="open" x-transition x-cloak
            class="absolute left-0 right-0 mt-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden z-40">
            <template x-for="(label, key) in skillOptions" :key="key">
              <a href="#"
                @click.prevent="open = false; selectTab(key)"
                class="flex items-center justify-between px-4 py-2.5 text-sm font-medium transition-colors duration-150"
                :class="activeTab === key
                    ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 font-semibold'
                    : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'">
                <span x-text="label"></span>
                <i class="fa fa-check text-blue-600 dark:text-blue-300 text-xs" x-show="activeTab === key"></i>
              </a>
            </template>
          </div>
        </div>
      </div>

      <!-- Tonal Center dropdown -->
      <div class="flex-1" x-data="{ open: false }" @click.outside="open = false">
        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tonal Center (Key)</label>
        <div class="relative">
          <button type="button" @click="open = !open"
            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 border-2 border-blue-200 dark:border-blue-900 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition">
            <span x-text="keyOptions[tonalCenter] ?? 'All Keys'"></span>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>

          <div x-show="open" x-transition x-cloak
            class="absolute left-0 right-0 mt-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden z-40 p-3">
            <a href="#"
              @click.prevent="open = false; selectKey('all')"
              class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150 mb-2"
              :class="tonalCenter === 'all'
                  ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 font-semibold'
                  : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'">
              All Keys
              <i class="fa fa-check text-blue-600 dark:text-blue-300 text-xs" x-show="tonalCenter === 'all'"></i>
            </a>
            <div class="grid grid-cols-3 gap-1.5 border-t border-gray-100 dark:border-gray-700 pt-2">
              @foreach ($tonalCenters as $value => $label)
                <a href="#"
                  @click.prevent="open = false; selectKey({{ \Illuminate\Support\Js::from($value) }})"
                  class="flex items-center justify-center gap-1 px-2 py-2 rounded-lg text-xs font-medium text-center whitespace-nowrap transition-colors duration-150"
                  :class="tonalCenter === {{ \Illuminate\Support\Js::from($value) }}
                      ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 font-semibold'
                      : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'">
                  {{ $label }}
                  <i class="fa fa-check text-blue-600 dark:text-blue-300 text-[10px]" x-show="tonalCenter === {{ \Illuminate\Support\Js::from($value) }}"></i>
                </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading indicator -->
    <div x-show="loading" x-cloak class="flex justify-center py-2">
        <div class="w-5 h-5 border-2 border-gray-300 border-t-indigo-500 rounded-full animate-spin"></div>
    </div>

    <!-- Song Cards -->
    <div id="learn-songs-results" x-ref="resultsContainer" :class="loading ? 'opacity-50' : ''" class="transition-opacity duration-150">
      @include('memberpages.partials.learnsongs-results')
    </div>

  </div>
</section>

</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

    .font-sans {
        font-family: 'Outfit', sans-serif;
    }
</style>

<script>
    function learnSongsPage(initialSearch, initialTab, initialKey) {
        return {
            search: initialSearch,
            activeTab: initialTab,
            tonalCenter: initialKey,
            loading: false,
            skillOptions: { all: 'All', beginner: 'Beginner', intermediate: 'Intermediate', advanced: 'Advanced' },
            keyOptions: @json($keyOptions),

            init() {
                this.$refs.resultsContainer.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (!link) return;
                    // Only intercept pagination links (same path, just a
                    // different ?page=). Song card links go to a different
                    // route entirely and must do a normal full navigation —
                    // otherwise that page's full HTML (header, nav, etc.)
                    // gets stuffed into this container instead of the browser
                    // actually navigating there.
                    if (link.pathname !== window.location.pathname) return;
                    e.preventDefault();
                    this.fetchResults(link.getAttribute('href'));
                });

                window.addEventListener('popstate', () => {
                    this.fetchResults(window.location.href, false);
                });
            },

            selectTab(key) {
                if (this.activeTab === key) return;
                this.activeTab = key;
                this.pushSearch();
            },

            selectKey(value) {
                if (this.tonalCenter === value) return;
                this.tonalCenter = value;
                this.pushSearch();
            },

            onSearchInput() {
                this.pushSearch();
            },

            clearSearch() {
                this.search = '';
                this.pushSearch();
            },

            pushSearch() {
                const params = new URLSearchParams();
                params.set('tab', this.activeTab);
                params.set('key', this.tonalCenter);
                if (this.search) params.set('search', this.search);
                this.fetchResults(window.location.pathname + '?' + params.toString());
            },

            async fetchResults(url, pushState = true) {
                this.loading = true;
                try {
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const html = await response.text();
                    this.$refs.resultsContainer.innerHTML = html;
                    if (pushState) {
                        window.history.pushState({}, '', url);
                    }
                } catch (error) {
                    console.error('Error loading songs:', error);
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
@endsection
