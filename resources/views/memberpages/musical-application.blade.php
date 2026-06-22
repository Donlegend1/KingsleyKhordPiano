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

    <div class="min-h-screen bg-[#F8FAFC] py-8 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-6xl mx-auto">

            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-8">
                <a href="{{ route('home') }}" class="hover:text-gray-700">Dashboard</a>
                <span>/</span>
                <span class="text-[#6366F1] font-medium">Musical Application</span>
            </div>

            <!-- Choose Your Level -->
            <div class="mb-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Choose Your Level</h2>
                <div class="flex flex-wrap gap-4">
                    @foreach ($skillLevels as $level)
                        <a href="{{ route('piano.exercise.musical', ['skill_level' => $level]) }}"
                            class="flex items-center gap-2 px-6 py-2.5 rounded-full border transition-all duration-300 font-medium
                       {{ $skillLevel === $level
                           ? 'bg-[#0FA9A0] border-[#0FA9A0] text-white shadow-md shadow-[#0fa9a0]/20'
                           : 'bg-white border-gray-200 text-gray-500 hover:border-[#0fa9a0] hover:text-[#0fa9a0]' }}">
                            <i class="fa-solid fa-chart-simple text-sm"></i>
                            {{ $level }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Courses Included -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-8">Courses Included</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($applications as $seriesName => $items)
                        @php $firstItem = $items->first(); @endphp
                        <div
                            class="bg-white rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 border border-gray-50 flex flex-col group">
                            <!-- Course Thumbnail -->
                            <div class="relative w-full h-48 rounded-xl overflow-hidden mb-6 bg-gray-100 shadow-sm">
                                <img src="{{ $firstItem->thumbnail_url ?? 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?q=80&w=800&auto=format&fit=crop' }}"
                                    alt="{{ $seriesName }}"
                                    class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                                <div
                                    class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-3 py-1 rounded-lg text-[11px] font-bold text-[#0FA9A0] uppercase tracking-wider shadow-sm">
                                    {{ count($items) }} Lessons
                                </div>
                            </div>

                            <!-- Course Info -->
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-[#0FA9A0] transition-colors">
                                {{ $seriesName }}
                            </h3>
                            <p class="text-gray-500 text-sm mb-8 leading-relaxed line-clamp-2">
                                {{ $firstItem->description ?? 'Apply your technique in real musical contexts and sound great.' }}
                            </p>
                            <a href="{{ route('piano.exercise.player', ['series' => $seriesName, 'skill_level' => strtolower($skillLevel)]) }}"
                                class="mt-auto w-full max-w-[140px] py-2.5 border border-gray-200 rounded-lg text-gray-700 font-semibold hover:bg-[#0FA9A0] hover:text-white hover:border-[#0FA9A0] transition-all duration-200 text-center">
                                Watch Now
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
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
