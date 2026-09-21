@extends('layouts.member')

@section('content')

    <div x-data="guidedPracticePage({{ \Illuminate\Support\Js::from($search ?? '') }}, {{ \Illuminate\Support\Js::from($skillLevel) }})" x-init="init()">

    <section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm text-gray-500 min-h-8">
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="hover:text-gray-700">Dashboard</a>
                <span>/</span>
                <span class="text-[#C85A5A] font-medium">Guided Practice</span>
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
                    placeholder="Search drills..."
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

    <div class="min-h-screen bg-[#F8FAFC] py-8 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-6xl mx-auto">

            <!-- Choose Your Level -->
            <div class="mb-12">
                <!-- Mobile: Dropdown -->
                <div class="sm:hidden relative" x-data="{ open: false }" @click.outside="open = false">
                    <button
                        type="button"
                        @click="open = !open"
                        style="background-color: #1447A6;"
                        class="w-full flex items-center justify-between px-6 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wide text-white shadow-md transition-all duration-300"
                    >
                        <span x-text="skillLevel"></span>
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
                        <template x-for="level in skillLevels" :key="level">
                            <a href="#"
                                @click.prevent="open = false; selectLevel(level)"
                                :style="skillLevel === level ? 'background-color: #1447A6;' : ''"
                                :class="skillLevel === level ? 'text-white' : 'text-gray-600 hover:bg-gray-50'"
                                class="block px-6 py-3 text-xs font-bold uppercase tracking-wide transition-colors duration-150"
                                x-text="level"
                            ></a>
                        </template>
                    </div>
                </div>

                <!-- Desktop: Segmented Control -->
                <div class="hidden sm:flex items-stretch bg-gray-100 rounded-lg overflow-hidden">
                    <template x-for="(level, index) in skillLevels" :key="level">
                        <a href="#"
                            @click.prevent="selectLevel(level)"
                            :style="skillLevel === level ? 'background-color: #1447A6;' : ''"
                            :class="[skillLevel === level ? 'text-white' : 'text-gray-700 hover:bg-gray-200', index !== 0 ? 'border-l border-gray-200' : '']"
                            class="flex-1 flex items-center justify-center text-center px-6 py-4 text-xs font-bold uppercase tracking-wide transition-colors duration-200"
                            x-text="level"
                        ></a>
                    </template>
                </div>
            </div>

            <!-- Loading indicator -->
            <div x-show="loading" x-cloak class="flex justify-center py-2">
                <div class="w-5 h-5 border-2 border-gray-300 border-t-[#1447A6] rounded-full animate-spin"></div>
            </div>

            <!-- Courses Included -->
            <div id="guided-practice-results" x-ref="resultsContainer" :class="loading ? 'opacity-50' : ''" class="transition-opacity duration-150">
                @include('memberpages.partials.musical-application-results')
            </div>

        </div>
    </div>

    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        .font-sans {
            font-family: 'Outfit', sans-serif;
        }
    </style>

    <script>
        function guidedPracticePage(initialSearch, initialLevel) {
            return {
                search: initialSearch,
                skillLevel: initialLevel,
                skillLevels: ['ALL', 'Beginner', 'Intermediate', 'Advanced'],
                loading: false,

                init() {
                    this.$refs.resultsContainer.addEventListener('click', (e) => {
                        const link = e.target.closest('a');
                        if (!link) return;
                        // Only intercept pagination links (same path, just a
                        // different ?page=). Drill/lesson links go to a
                        // different route entirely and must do a normal full
                        // navigation — otherwise that page's full HTML
                        // (header, nav, etc.) gets stuffed into this
                        // container instead of the browser navigating there.
                        if (link.pathname !== window.location.pathname) return;
                        e.preventDefault();
                        this.fetchResults(link.getAttribute('href'));
                    });

                    window.addEventListener('popstate', () => {
                        this.fetchResults(window.location.href, false);
                    });
                },

                selectLevel(level) {
                    if (this.skillLevel === level) return;
                    this.skillLevel = level;
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
                    params.set('skill_level', this.skillLevel);
                    if (this.search) params.set('name', this.search);
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
                        console.error('Error loading drills:', error);
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
@endsection
