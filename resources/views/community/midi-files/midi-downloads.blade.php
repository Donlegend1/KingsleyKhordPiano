@extends('layouts.community')

@section('content')
<!-- Header Section -->
<div class="bg-white dark:bg-gray-800 mb-6">
    <div class="px-4 sm:px-6 py-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="/member/community" class="hover:text-[#FFD736]">Community</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Midi Files</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-[#1F2937] dark:text-white">Midi Files</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">Download MIDI files organized by skill level to practice and learn</p>
    </div>
</div>

<!-- Main Content Section -->
<section class="px-4 sm:px-6 pb-6 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto">

        <!-- MIDI Files Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
           @php
    // colors rotate if no thumbnail exists
    $gradients = [
        ['from' => 'from-blue-400', 'to' => 'to-blue-600'],
        ['from' => 'from-green-400', 'to' => 'to-green-600'],
        ['from' => 'from-purple-400', 'to' => 'to-purple-600'],
        ['from' => 'from-yellow-400', 'to' => 'to-yellow-600'],
        ['from' => 'from-orange-400', 'to' => 'to-orange-600'],
        ['from' => 'from-pink-400', 'to' => 'to-pink-600'],
    ];
@endphp

@foreach ($midiFiles as $index => $midiFile)
    @php
        $g = $gradients[$index % count($gradients)];  
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">

        {{-- Header Visual --}}
        @if ($midiFile->thumbnail_path)
            {{-- SHOW IMAGE IF AVAILABLE --}}
            <div class="relative h-32 overflow-hidden">
                <img 
                    src="/{{$midiFile->thumbnail_path}}" 
                    alt="{{ $midiFile->name }}" 
                    class="w-full h-full object-cover"
                >
            </div>
        @else
            {{-- USE ALTERNATING COLORS --}}
            <div class="relative h-32 bg-gradient-to-br {{ $g['from'] }} {{ $g['to'] }} flex items-center justify-center p-4">
                <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.23 12.004a2.236 2.236 0 0 1 2.235 2.236A2.236 2.236 0 0 1 14.23 16.476a2.236 2.236 0 0 1-2.235-2.236 2.236 2.236 0 0 1 2.235-2.236zm2.648-10.69c.366 0 .662.297.662.662v3.34c1.154.28 2.01 1.32 2.01 2.585a2.67 2.67 0 0 1-2.667 2.667h-4.666a2.67 2.67 0 0 1-2.667-2.667c0-1.265.856-2.305 2.01-2.585V1.976c0-.365.296-.662.662-.662h2.326zm-.662 4.666c-.735 0-1.333.598-1.333 1.333 0 .735.598 1.333 1.333 1.333h4.666c.735 0 1.333-.598 1.333-1.333 0-.735-.598-1.333-1.333-1.333h-4.666z"/>
                </svg>
                <div class="absolute inset-0 opacity-10 bg-black"></div>
            </div>
        @endif

        {{-- Body --}}
        <div class="p-5">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                {{ $midiFile->name }}
            </h3>

            <a href="{{ url('member/community/space/midi-download/' . $midiFile->id) }}"
               class="w-full bg-[#FF6B35] hover:bg-[#E55A2B] text-white py-2.5 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                VIEW
            </a>
        </div>
    </div>
@endforeach

        </div>

    </div>
</section>
@endsection
