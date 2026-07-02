@extends('layouts.member')

@section('content')

@php
    $cards = [
        [
            'id' => 'independence',
            'title' => 'Hand Independence',
            'desc' => 'Develop the ability to move each hand independently with control.',
        ],
        [
            'id' => 'flexibility',
            'title' => 'Hand Flexibility',
            'desc' => 'Improve your range of motion and adapt to different musical situations.',
        ],
        [
            'id' => 'dexterity',
            'title' => 'Hand Dexterity',
            'desc' => 'Enhance finger agility and coordination for smooth execution.',
        ],
        [
            'id' => 'strength',
            'title' => 'Finger Strength',
            'desc' => 'Build finger strength and endurance for powerful playing.',
        ],
        [
            'id' => 'technique',
            'title' => 'Technique',
            'desc' => 'Sharpen your overall piano technique with focused, guided drills.',
        ],
    ];
@endphp

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
    <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-gray-700">Dashboard</a>
        <span>/</span>
        <a href="{{ route('piano.exercise') }}" class="hover:text-gray-700">Piano Exercise</a>
        <span>/</span>
        <span class="text-[#6366F1] font-medium">Finger Exercises</span>
    </div>
</section>

<div class="min-h-screen bg-[#F4F5F7] py-10 px-4" x-data="{ activeTab: 'finger' }">
    <div class="max-w-6xl mx-auto">

        <!-- Tabs -->
        <div class="flex justify-center mb-10">
            <div class="inline-flex items-center gap-8 border-b border-gray-200">
                <button type="button" @click="activeTab = 'finger'"
                    class="relative pb-3 text-sm font-medium transition-colors duration-200"
                    :class="activeTab === 'finger' ? 'text-[#6366F1] font-semibold' : 'text-gray-400 hover:text-gray-600'">
                    Finger Exercise
                    <span class="absolute left-0 right-0 -bottom-px h-0.5 rounded-full bg-[#6366F1] transition-opacity duration-200"
                          :class="activeTab === 'finger' ? 'opacity-100' : 'opacity-0'"></span>
                </button>
                <button type="button" @click="activeTab = 'etudes'"
                    class="relative pb-3 text-sm font-medium transition-colors duration-200"
                    :class="activeTab === 'etudes' ? 'text-[#6366F1] font-semibold' : 'text-gray-400 hover:text-gray-600'">
                    Etudes &amp; Pieces
                    <span class="absolute left-0 right-0 -bottom-px h-0.5 rounded-full bg-[#6366F1] transition-opacity duration-200"
                          :class="activeTab === 'etudes' ? 'opacity-100' : 'opacity-0'"></span>
                </button>
            </div>
        </div>

        <!-- Finger Exercise Tab -->
        <div x-show="activeTab === 'finger'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($cards as $card)
            @php
                $cardLessons = \App\Models\Upload::where('category', 'piano exercise')
                    ->where('level', $card['id'])
                    ->where('status', 'active')
                    ->orderBy('id')
                    ->get();
                $firstLesson = $cardLessons->first();
                $lessonCount = $cardLessons->count();

                $newCardLesson = $cardLessons
                    ->filter(fn($l) => $l->created_at && $l->created_at->gt(now()->subDays(7)))
                    ->sortByDesc('created_at')
                    ->first();
                $isNew = $newCardLesson && !\App\Models\LessonView::hasViewed(auth()->id(), $newCardLesson);

                $watchUrl = route('piano.exercise.player', ['level' => $card['id']]);
            @endphp
            <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col group">

                <!-- Thumbnail -->
                <a href="{{ $watchUrl }}" class="block relative overflow-hidden" style="aspect-ratio:16/9;">
                    @if($firstLesson && $firstLesson->thumbnail_url)
                        <img src="{{ $firstLesson->thumbnail_url }}" alt="{{ $card['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-900 to-purple-900">
                            <i class="fa fa-hand-paper text-5xl text-white/30"></i>
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
                        {{ $card['title'] }}
                    </h3>

                    <p class="text-gray-400 text-sm leading-relaxed flex-1">{{ $card['desc'] }}</p>

                    <!-- Level Badge -->
                    <div>
                        <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-600 text-xs font-semibold px-2.5 py-1 rounded-full border border-indigo-100">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            All Levels
                        </span>
                    </div>

                    <!-- Watch Now Button -->
                    <a href="{{ $watchUrl }}"
                       class="mt-auto flex items-center justify-center w-full py-3 bg-[#6366F1] hover:bg-[#4F46E5] text-white text-sm font-bold rounded-xl transition-all duration-200">
                        Watch Now
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Etudes & Pieces Tab -->
        <div x-show="activeTab === 'etudes'" x-cloak>
            {{-- Content coming soon --}}
        </div>

    </div>
</div>

@endsection
