@extends('layouts.member')

@section('content')

@php
    $levels = ['Basic', 'Competent', 'Challenging'];
    $cards = [
        [
            'id' => 'independence',
            'title' => 'Independence',
            'desc' => 'Develop the ability to move each hand independently with control.',
            'icon' => 'fa-child-reaching',
            'img' => '/images/hand-independence.jpeg'
        ],
        [
            'id' => 'flexibility',
            'title' => 'Flexibility',
            'desc' => 'Improve your range of motion and adapt to different musical situations.',
            'icon' => 'fa-arrows-left-right',
            'img' => '/images/hand-flexibility.jpeg'
        ],
        [
            'id' => 'dexterity',
            'title' => 'Dexterity',
            'desc' => 'Enhance finger agility and coordination for smooth execution.',
            'icon' => 'fa-hand',
            'img' => '/images/hands-dexterity.jpeg'
        ],
        [
            'id' => 'strength',
            'title' => 'Strength',
            'desc' => 'Build finger strength and endurance for powerful playing.',
            'icon' => 'fa-dumbbell',
            'img' => '/images/finger-strength.jpeg'
        ],
        [
            'id' => 'technique',
            'title' => 'Technique',
            'desc' => 'Refine your overall technique for expressive playing.',
            'icon' => 'fa-sliders',
            'img' => '/images/technique.jpeg'
        ],
    ];
@endphp

<div class="min-h-screen bg-[#ECEEF2] py-10 px-4">
    <div class="max-w-5xl mx-auto">

        <!-- Skill Level Toggle — 3 separate pill buttons -->
        <div class="flex justify-center items-center gap-3 mb-12">
            @foreach($levels as $index => $level)
                @php $isActive = strtolower($skillLevel) === strtolower($level); @endphp
                <a href="{{ route('piano.exercise.finger', ['skill_level' => $level]) }}"
                   class="flex items-center gap-2.5 px-8 py-3 rounded-full border transition-all duration-200 shadow-sm
                   {{ $isActive
                       ? 'bg-blue-500 border-blue-500 text-white shadow-md'
                       : 'bg-white border-gray-200 text-gray-400 hover:text-gray-600 hover:border-gray-300' }}">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                        {{ $isActive ? 'bg-white text-blue-500' : 'bg-gray-100 text-gray-400' }}">
                        {{ $index + 1 }}
                    </span>

                    <span class="font-semibold text-[15px]">{{ $level }}</span>
                </a>
            @endforeach
        </div>

        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-[28px] font-bold text-gray-900 mb-2">Choose Your Focus Area</h1>
            <p class="text-gray-400 text-[15px]">Select the skill you want to develop</p>
        </div>

        <!-- Row 1: exactly 3 cards -->
        <div class="grid grid-cols-3 gap-6 mb-14">
            @foreach(array_slice($cards, 0, 3) as $card)
            <div class="relative flex flex-col items-center bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 pt-8 pb-7 px-5 border border-gray-100">

                <!-- Floating icon badge — sits above the card top edge -->
                {{-- <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-white rounded-full shadow-md border border-gray-100 flex items-center justify-center z-10">
                    <i class="fa-solid {{ $card['icon'] }} text-[#0AADA3] text-[17px]"></i>
                </div> --}}

                <!-- Image with teal overlay -->
                <div class="w-full rounded-xl overflow-hidden mb-5 relative" style="height: 170px;">
                    <img src="{{ $card['img'] }}" alt="{{ $card['title'] }}"
                         class="w-full h-full object-cover" />
                    <div class="absolute inset-0" style="background: rgba(10, 120, 110, 0.55); mix-blend-mode: multiply;"></div>
                </div>

                <!-- Title -->
                <h2 class="text-[17px] font-bold text-gray-900 mb-2 text-center">{{ $card['title'] }}</h2>

                <!-- Desc -->
                <p class="text-gray-400 text-[13px] text-center leading-relaxed mb-6" style="min-height: 44px;">
                    {{ $card['desc'] }}
                </p>

                <!-- Select button -->
                <a href="{{ route('piano.exercise.player', ['level' => $card['id'], 'skill_level' => strtolower($skillLevel)]) }}"
                   class="w-full text-center border border-gray-200 hover:border-gray-400 text-gray-800 font-semibold text-[14px] py-2.5 rounded-xl hover:bg-gray-50 transition-all duration-200">
                    Watch Now
                </a>
            </div>
            @endforeach
        </div>

        <!-- Row 2: exactly 2 cards, centered -->
        <div class="flex justify-center gap-6">
            @foreach(array_slice($cards, 3, 2) as $card)
            <div class="relative flex flex-col items-center bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 pt-8 pb-7 px-5 border border-gray-100" style="width: 320px;">

                <!-- Floating icon badge -->
                {{-- <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 bg-white rounded-full shadow-md border border-gray-100 flex items-center justify-center z-10">
                    <i class="fa-solid {{ $card['icon'] }} text-[#0AADA3] text-[17px]"></i>
                </div> --}}

                <!-- Image with teal overlay -->
                <div class="w-full rounded-xl overflow-hidden mb-5 relative" style="height: 170px;">
                    <img src="{{ $card['img'] }}" alt="{{ $card['title'] }}"
                         class="w-full h-full object-cover" />
                    <div class="absolute inset-0" style="background: rgba(10, 120, 110, 0.55); mix-blend-mode: multiply;"></div>
                </div>

                <!-- Title -->
                <h2 class="text-[17px] font-bold text-gray-900 mb-2 text-center">{{ $card['title'] }}</h2>

                <!-- Desc -->
                <p class="text-gray-400 text-[13px] text-center leading-relaxed mb-6" style="min-height: 44px;">
                    {{ $card['desc'] }}
                </p>

                <!-- Select button -->
                <a href="{{ route('piano.exercise.player', ['level' => $card['id'], 'skill_level' => strtolower($skillLevel)]) }}"
                   class="w-full text-center border border-gray-200 hover:border-gray-400 text-gray-800 font-semibold text-[14px] py-2.5 rounded-xl hover:bg-gray-50 transition-all duration-200">
                    Watch Now
                </a>
            </div>
            @endforeach
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
@endsection