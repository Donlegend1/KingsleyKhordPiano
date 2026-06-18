@extends('layouts.member')

@section('content')

@php
    $levels = ['Basic', 'Competent', 'Challenging'];
    $cards = [
        [
            'id' => 'independence',
            'title' => 'Independence',
            'desc' => 'Develop the ability to move each hand independently with control.',
            'img' => '/images/hand-independence.jpeg'
        ],
        [
            'id' => 'flexibility',
            'title' => 'Flexibility',
            'desc' => 'Improve your range of motion and adapt to different musical situations.',
            'img' => '/images/hand-flexibility.jpeg'
        ],
        [
            'id' => 'dexterity',
            'title' => 'Dexterity',
            'desc' => 'Enhance finger agility and coordination for smooth execution.',
            'img' => '/images/hands-dexterity.jpeg'
        ],
        [
            'id' => 'strength',
            'title' => 'Strength',
            'desc' => 'Build finger strength and endurance for powerful playing.',
            'img' => '/images/finger-strength.jpeg'
        ],
    ];
@endphp

<div class="min-h-screen bg-[#F4F5F7] py-10 px-4">
    <div class="max-w-4xl mx-auto">

        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-gray-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('piano.exercise') }}" class="hover:text-gray-700">Piano Exercise</a>
            <span>/</span>
            <span class="text-[#6366F1] font-medium">Finger Exercises</span>
        </div>

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

        <!-- 2x2 Card Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @foreach($cards as $card)
            <div class="flex flex-col bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 group">

                <!-- Image -->
                <div class="w-full overflow-hidden" style="height: 180px;">
                    <img src="{{ $card['img'] }}" alt="{{ $card['title'] }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                </div>

                <!-- Content -->
                <div class="p-5 flex flex-col flex-1">
                    <h2 class="text-base font-bold text-gray-900 mb-1">{{ $card['title'] }}</h2>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5 flex-1">{{ $card['desc'] }}</p>

                    <a href="{{ route('piano.exercise.player', ['level' => $card['id'], 'skill_level' => strtolower($skillLevel)]) }}"
                       class="w-full text-center bg-[#6366F1] hover:bg-[#4F46E5] text-white font-semibold text-sm py-2.5 rounded-xl transition-colors duration-200">
                        Watch Now
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

@endsection
