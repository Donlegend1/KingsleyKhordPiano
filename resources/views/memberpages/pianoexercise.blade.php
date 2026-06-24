@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
    <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-gray-700">Dashboard</a>
        <span>/</span>
        <span class="text-[#6366F1] font-medium">Piano Exercise</span>
    </div>
</section>

<div class="min-h-screen bg-[#F8FAFC] font-sans">
    <div class="max-w-5xl mx-auto px-6 py-10">

        <!-- Page Title -->
        <div class="text-center mb-20">
            <h1 class="text-3xl font-extrabold text-[#1E293B] mb-3">Choose a Path</h1>
            <p class="font-semibold text-[16px] max-w-xl mx-auto leading-relaxed text-[#64748B]">
                Strengthen your technique or put it into practice in real musical contexts.
            </p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">

            <!-- Card 1: Finger Exercises -->
            <div class="relative bg-gradient-to-br from-white to-indigo-50 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 overflow-hidden">

                <!-- Decorative waves -->
                <svg class="absolute bottom-0 right-0 w-40 h-40 text-indigo-200 opacity-60" viewBox="0 0 160 160" fill="none">
                    <path d="M20 140 C50 110, 70 160, 100 130 S150 90, 160 110" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M30 150 C60 120, 80 170, 110 140 S160 100, 170 120" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M10 130 C40 100, 60 150, 90 120 S140 80, 150 100" stroke="currentColor" stroke-width="1.5"/>
                </svg>

                <!-- Icon Badge -->
                <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-[#6366F1]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18V6a1 1 0 0 1 2 0v12M5 18v-8a1 1 0 0 1 2 0v8M13 18V6a1 1 0 0 1 2 0v12M3 18h18"/>
                    </svg>
                </div>

                <!-- Content -->
                <h2 class="text-3xl font-extrabold text-[#1E293B] leading-tight mb-1">Finger<br><span class="text-[#6366F1]">Exercises</span></h2>
                <div class="w-10 h-1 rounded-full bg-[#6366F1] mb-5"></div>
                <p class="text-[#64748B] text-[15px] leading-relaxed mb-8 max-w-[260px] relative z-10">
                    Build strength, independence, flexibility, and control with targeted exercises.
                </p>

                <!-- Button -->
                <a href="{{ route('piano.exercise.finger', ['skill_level' => 'Basic']) }}"
                   class="relative z-10 inline-flex items-center gap-2 bg-[#6366F1] hover:bg-[#4F46E5] text-white px-7 py-3 rounded-full font-semibold transition-all duration-300 shadow-md shadow-[#6366f1]/20 hover:shadow-lg hover:shadow-[#6366f1]/30 hover:-translate-y-0.5 text-[14px]">
                    Explore <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Card 2: Musical Application -->
            <div class="relative bg-gradient-to-br from-white to-amber-50 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 overflow-hidden">

                <!-- Decorative waves -->
                <svg class="absolute bottom-0 right-0 w-40 h-40 text-amber-200 opacity-60" viewBox="0 0 160 160" fill="none">
                    <path d="M20 140 C50 110, 70 160, 100 130 S150 90, 160 110" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M30 150 C60 120, 80 170, 110 140 S160 100, 170 120" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M10 130 C40 100, 60 150, 90 120 S140 80, 150 100" stroke="currentColor" stroke-width="1.5"/>
                </svg>

                <!-- Icon Badge -->
                <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18V5l12-2v13M9 18a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm12-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                </div>

                <!-- Content -->
                <h2 class="text-3xl font-extrabold text-[#1E293B] leading-tight mb-1">Technique<br><span class="text-amber-500">Drills</span></h2>
                <div class="w-10 h-1 rounded-full bg-amber-400 mb-5"></div>
                <p class="text-[#64748B] text-[15px] leading-relaxed mb-8 max-w-[260px] relative z-10">
                    Sharpen your technique with focused drills designed to improve speed, accuracy, and control.
                </p>

                <!-- Button -->
                <a href="{{ route('piano.exercise.musical') }}"
                   class="relative z-10 inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-500 text-[#1E293B] px-7 py-3 rounded-full font-semibold transition-all duration-300 shadow-md shadow-amber-400/20 hover:shadow-lg hover:shadow-amber-400/30 hover:-translate-y-0.5 text-[14px]">
                    Explore <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection