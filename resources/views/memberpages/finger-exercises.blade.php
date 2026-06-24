@extends('layouts.member')

@section('content')

@php
    $levels = ['Basic', 'Competent', 'Challenging'];
    $cards = [
        [
            'id' => 'independence',
            'title' => 'Hand Independence',
            'desc' => 'Develop the ability to move each hand independently with control.',
            'bg' => 'bg-blue-50',
            'stroke' => 'text-blue-500',
        ],
        [
            'id' => 'flexibility',
            'title' => 'Hand Flexibility',
            'desc' => 'Improve your range of motion and adapt to different musical situations.',
            'bg' => 'bg-blue-50',
            'stroke' => 'text-blue-500',
        ],
        [
            'id' => 'dexterity',
            'title' => 'Hand Dexterity',
            'desc' => 'Enhance finger agility and coordination for smooth execution.',
            'bg' => 'bg-orange-50',
            'stroke' => 'text-orange-500',
        ],
        [
            'id' => 'strength',
            'title' => 'Finger Strength',
            'desc' => 'Build finger strength and endurance for powerful playing.',
            'bg' => 'bg-green-50',
            'stroke' => 'text-green-500',
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

<div class="min-h-screen bg-[#F4F5F7] py-10 px-4">
    <div class="max-w-6xl mx-auto">

        <!-- Skill Level Toggle -->
        <div class="flex justify-center mb-10">
            <div class="inline-flex flex-col sm:flex-row bg-gray-100 rounded-2xl p-1.5 gap-1">
                @foreach($levels as $index => $level)
                    @php $isActive = strtolower($skillLevel) === strtolower($level); @endphp
                    <a href="{{ route('piano.exercise.finger', ['skill_level' => $level]) }}"
                       class="relative flex items-center justify-center gap-2 px-7 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 min-w-[120px]
                       {{ $isActive
                           ? 'bg-[#6366F1] text-white shadow-md scale-[1.03]'
                           : 'text-gray-400 hover:text-gray-600' }}">
                        @if($isActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-white/70 absolute top-2 right-2"></span>
                        @endif
                        {{ $level }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Choose Your Focus Area</h1>
            <p class="text-gray-400 text-sm">Select the skill you want to develop</p>
        </div>

        <!-- Card Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($cards as $card)
            <div class="flex flex-col items-center text-center bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 p-6">

                <!-- Icon Badge -->
                <div class="w-20 h-20 rounded-full {{ $card['bg'] }} flex items-center justify-center mb-5">
                    <svg class="w-9 h-9 {{ $card['stroke'] }}" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 11V6a2 2 0 0 0-4 0v0M14 10V4a2 2 0 0 0-4 0v2M10 10.5V6a2 2 0 0 0-4 0v8M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/>
                    </svg>
                </div>

                <!-- Content -->
                <h2 class="text-base font-bold text-gray-900 mb-2">{{ $card['title'] }}</h2>
                <p class="text-gray-400 text-sm leading-relaxed mb-5 flex-1">{{ $card['desc'] }}</p>

                <a href="{{ route('piano.exercise.player', ['level' => $card['id'], 'skill_level' => strtolower($skillLevel)]) }}"
                   class="w-full flex items-center justify-center gap-2 border border-indigo-200 text-[#6366F1] font-semibold text-sm py-2.5 rounded-xl hover:bg-indigo-50 transition-colors duration-200">
                    Watch Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>

    </div>
</div>

@endsection
