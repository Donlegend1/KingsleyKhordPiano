@extends('layouts.community')

@section('breadcrumb-parent', 'Overview')
@section('breadcrumb-parent-url', '/member/my-library')
@section('breadcrumb', 'Audio Files')

@section('page-search')
    <div class="relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"/>
        </svg>
        <input
            type="text"
            x-model="search"
            placeholder="Search audio files..."
            class="w-full pl-11 pr-4 py-2.5 rounded-full border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161617] text-sm text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF6B35]/30 focus:border-[#FF6B35] transition"
        >
    </div>
@endsection

@section('content')

<!-- Main Content Section -->
<section class="px-4 sm:px-6 pt-6 pb-6 bg-gray-50 dark:bg-black">
    <div class="max-w-7xl mx-auto">

        <!-- Tabs Navigation -->
        @php
            $allAudio = $pianoPlays->concat($tracksAndLoops)->sortByDesc('created_at')->values();
        @endphp
        <div class="mb-8" x-data="{ activeTab: 'all' }">

            <!-- Tab Buttons - Spread out on Desktop, Stacked on Mobile -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-6">
                <button
                    @click="activeTab = 'all'"
                    :class="activeTab === 'all' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-[#161617] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10'"
                    class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    All Files
                </button>

                  <button
                    @click="activeTab = 'intermediate'"
                    :class="activeTab === 'intermediate' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-[#161617] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10'"
                    class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    Piano Plays
                </button>

                <button
                    @click="activeTab = 'beginners'"
                    :class="activeTab === 'beginners' ? 'bg-[#FF6B35] text-white' : 'bg-white dark:bg-[#161617] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10'"
                    class="flex-1 px-8 py-3 rounded-lg font-semibold text-sm sm:text-base transition-all duration-200 shadow-sm"
                >
                    Track & Loops
                </button>



            </div>

            <!-- Tab Content -->
            <div class="min-h-[500px]">

                <!-- All Files Content -->
                <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    @if($allAudio->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($allAudio as $audio)
                                <div x-show="search === '' || {{ \Illuminate\Support\Js::from(Str::lower($audio->title)) }}.includes(search.toLowerCase())">
                                    @include('community.partials.audio-card', ['audio' => $audio])
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M9 19V5m0 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
                            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No Audio Available</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for audio resources</p>
                        </div>
                    @endif
                </div>

                <!-- Track & Loops Content -->
                <div x-show="activeTab === 'beginners'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    @if($tracksAndLoops->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($tracksAndLoops as $audio)
                                <div x-show="search === '' || {{ \Illuminate\Support\Js::from(Str::lower($audio->title)) }}.includes(search.toLowerCase())">
                                    @include('community.partials.audio-card', ['audio' => $audio])
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M9 19V5m0 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
                            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No Audio Available</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for track and loop resources</p>
                        </div>
                    @endif
                </div>

                <!-- Piano Plays Content -->
                <div x-show="activeTab === 'intermediate'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    @if($pianoPlays->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($pianoPlays as $audio)
                                <div x-show="search === '' || {{ \Illuminate\Support\Js::from(Str::lower($audio->title)) }}.includes(search.toLowerCase())">
                                    @include('community.partials.audio-card', ['audio' => $audio])
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 dark:text-gray-600 mb-4"><path d="M9 19V5m0 0a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2z"/></svg>
                            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-300">No Audio Available</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for piano play resources</p>
                        </div>
                    @endif
                </div>


            </div>
        </div>

    </div>
</section>

@endsection

