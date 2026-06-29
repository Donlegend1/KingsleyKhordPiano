@extends('layouts.member')

@section('content')
    @php
        $cards = [
            [
                'title' => 'DROP 2S',
                'desc' => 'Discover rich voicings and smooth movement using drop 2 inversions.',
                'img' => 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=800&auto=format&fit=crop',
                'badge' => 'Beginner',
            ],
            [
                'title' => 'GRACE NOTES',
                'desc' => 'Add expression and elegance with grace notes and embellishments.',
                'img' => 'https://images.unsplash.com/photo-1552422535-c45813c61732?q=80&w=800&auto=format&fit=crop',
                'badge' => 'Beginner',
            ],
            [
                'title' => 'TRIAD PAIRING',
                'desc' => 'Combine triads to create beautiful harmonic progressions.',
                'img' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=800&auto=format&fit=crop',
                'badge' => 'Beginner',
            ],
            [
                'title' => 'CHORDAL HARMONY',
                'desc' => 'Expand your sound with harmonically rich chord structures.',
                'img' => 'https://images.unsplash.com/photo-1598514982205-f36b96d1e8d4?q=80&w=800&auto=format&fit=crop',
                'badge' => 'Beginner',
            ],
            [
                'title' => 'DI TONES',
                'desc' => 'Explore tension and color with diatonic passing tones.',
                'img' => 'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?q=80&w=800&auto=format&fit=crop',
                'badge' => 'Beginner',
            ],
            [
                'title' => 'DIMINISHED 7THS',
                'desc' => 'Unlock sophisticated sounds with diminished 7th chords.',
                'img' => 'https://images.unsplash.com/photo-1514119412350-e174d90d280e?q=80&w=800&auto=format&fit=crop',
                'badge' => 'Beginner',
            ],
        ];
    @endphp

    <section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
        <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-gray-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('piano.exercise') }}" class="hover:text-gray-700">Piano Exercise</a>
            <span>/</span>
            <span class="text-[#6366F1] font-medium">Technique Drills</span>
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
                        class="w-full flex items-center justify-between px-6 py-2.5 rounded-full font-semibold bg-indigo-600 text-white shadow-md shadow-indigo-600/20 transition-all duration-300"
                    >
                        <span>{{ $skillLevel }}</span>
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
                        @foreach ($skillLevels as $level)
                            <a href="{{ route('piano.exercise.musical', ['skill_level' => $level]) }}"
                                class="block px-6 py-3 font-semibold transition-colors duration-150
                           {{ $skillLevel === $level
                               ? 'bg-indigo-600 text-white'
                               : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ $level }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Desktop: Pills -->
                <div class="hidden sm:flex flex-wrap items-center gap-6">
                    @foreach ($skillLevels as $level)
                        <a href="{{ route('piano.exercise.musical', ['skill_level' => $level]) }}"
                            class="px-6 py-2.5 rounded-full font-semibold transition-all duration-300
                       {{ $skillLevel === $level
                           ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                           : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $level }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Courses Included -->
            <div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($applications as $seriesName => $items)
                        @php
                            $firstItem = $items->first();
                            $lessonCount = count($items);
                            $playerUrl = route('piano.exercise.player', ['series' => $seriesName, 'skill_level' => strtolower($firstItem->skill_level)]);
                            $isNew = !\App\Models\LessonView::hasViewed(auth()->id(), $firstItem)
                                && $items->contains(fn ($i) => $i->created_at && $i->created_at->gt(now()->subDays(7)));
                        @endphp
                        <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col group">

                            <!-- Thumbnail -->
                            <a href="{{ $playerUrl }}" class="block relative overflow-hidden" style="aspect-ratio:16/9;">
                                @if($firstItem->thumbnail_url ?? null)
                                    <img src="{{ $firstItem->thumbnail_url }}" alt="{{ $seriesName }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
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
                                    {{ $seriesName }}
                                </h3>

                                <!-- Level Badge -->
                                <div>
                                    <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-full border border-indigo-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        {{ $firstItem->skill_level }}
                                    </span>
                                </div>

                                <!-- Watch Now Button -->
                                <a href="{{ $playerUrl }}"
                                   class="mt-auto flex items-center justify-center w-full py-3 bg-[#6366F1] hover:bg-[#4F46E5] text-white text-sm font-bold rounded-xl transition-all duration-200">
                                    Watch Now
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100">
                            <i class="fa-regular fa-folder-open text-gray-200 text-6xl block mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-800">No applications found</h3>
                            <p class="text-gray-400">Try selecting a different skill level or check back later.</p>
                        </div>
                    @endforelse
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
@endsection
