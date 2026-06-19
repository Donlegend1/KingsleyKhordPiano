@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] font-sans">
    <div class="max-w-5xl mx-auto px-6 py-10">

        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-gray-700">Dashboard</a>
            <span>/</span>
            <span class="text-[#6366F1] font-medium">Piano Exercise</span>
        </div>

        <!-- Page Title -->
        <div class="text-center mb-20">
            <p class="font-semibold text-[16px] max-w-xl mx-auto leading-relaxed" style="color: #F8FAFC;">
                Choose your path to build stronger fingers and apply your skills musically.
            </p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 max-w-4xl mx-auto">

            <!-- Card 1: Finger Exercises -->
            <div class="bg-white rounded-3xl p-6 pb-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative flex flex-col items-center">

                <!-- Icon Badge -->
                <div class="absolute -top-9 bg-[#6366F1] text-white w-[68px] h-[68px] rounded-full flex items-center justify-center border-[5px] border-[#F8FAFC] shadow-sm z-10">
                    <i class="fa-solid fa-hand-paper text-[26px]"></i>
                </div>

                <!-- Image -->
                <div class="w-full h-52 rounded-2xl overflow-hidden mt-8 mb-6 shadow-sm">
                    <img src="/images/finger-exercise.jpeg"
                         alt="Finger Exercises"
                         class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700">
                </div>

                <!-- Content -->
                <h2 class="text-[20px] font-bold text-[#1E293B] mb-2">Finger Exercises</h2>
                <p class="text-[#64748B] text-center mb-8 px-2 text-[14px] leading-relaxed">
                    Build strength, independence, flexibility,<br>and control with targeted exercises.
                </p>

                <!-- Button -->
                <a href="{{ route('piano.exercise.finger', ['skill_level' => 'Basic']) }}"
                   class="mt-auto bg-[#6366F1] hover:bg-[#4F46E5] text-white px-8 py-3 rounded-full font-medium transition-all duration-300 flex items-center gap-2 shadow-md shadow-[#6366f1]/20 hover:shadow-lg hover:shadow-[#6366f1]/30 w-max mx-auto hover:-translate-y-0.5 text-[14px]">
                    Explore <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Card 2: Musical Application -->
            <div class="bg-white rounded-3xl p-6 pb-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative flex flex-col items-center">

                <!-- Icon Badge -->
                <div class="absolute -top-9 bg-[#6366F1] text-white w-[68px] h-[68px] rounded-full flex items-center justify-center border-[5px] border-[#F8FAFC] shadow-sm z-10">
                    <i class="fa-solid fa-music text-[26px]"></i>
                </div>

                <!-- Image -->
                <div class="w-full h-52 rounded-2xl overflow-hidden mt-8 mb-6 shadow-sm bg-black">
                    <img src="/images/musical-application.jpeg"
                         alt="Musical Application"
                         class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700 opacity-90">
                </div>

                <!-- Content -->
                <h2 class="text-[20px] font-bold text-[#1E293B] mb-2">Musical Application</h2>
                <p class="text-[#64748B] text-center mb-8 px-2 text-[14px] leading-relaxed">
                    Apply your technique in real musical<br>contexts and sound great.
                </p>

                <!-- Button -->
                <a href="{{ route('piano.exercise.musical') }}"
                   class="mt-auto bg-[#6366F1] hover:bg-[#4F46E5] text-white px-8 py-3 rounded-full font-medium transition-all duration-300 flex items-center gap-2 shadow-md shadow-[#6366f1]/20 hover:shadow-lg hover:shadow-[#6366f1]/30 w-max mx-auto hover:-translate-y-0.5 text-[14px]">
                    Explore <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection