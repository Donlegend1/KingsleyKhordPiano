@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] font-sans">
    <div class="max-w-5xl mx-auto px-6 py-10">

        <!-- Breadcrumb -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 flex-wrap mb-6">
            <a href="{{ route('home') }}"
               class="flex items-center gap-1.5 text-[13px] text-[#64748B] hover:text-[#1E293B] hover:bg-slate-100 px-2.5 py-1.5 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Home
            </a>

            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-slate-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="9 18 15 12 9 6"/>
            </svg>

            <span class="text-[13px] font-bold text-[#0FA9A0]  px-2.5 py-1.5 rounded-lg bg-[#0FA9A0]/8" aria-current="page">
                Piano Exercise
            </span>
        </nav>

        <!-- Page Title -->
        <div class="text-center mb-20">
            <p class="text-[#64748B] font-semibold text-[16px] max-w-xl mx-auto leading-relaxed">
                Choose your path to build stronger fingers and apply your skills musically.
            </p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 max-w-4xl mx-auto">

            <!-- Card 1: Finger Exercises -->
            <div class="bg-white rounded-3xl p-6 pb-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative flex flex-col items-center">

                <!-- Icon Badge -->
                <div class="absolute -top-9 bg-[#0FA9A0] text-white w-[68px] h-[68px] rounded-full flex items-center justify-center border-[5px] border-[#F8FAFC] shadow-sm z-10">
                    <i class="fa-solid fa-hand-paper text-[26px]"></i>
                </div>

                <!-- Image -->
                <div class="w-full h-52 rounded-2xl overflow-hidden mt-8 mb-6 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1552422535-c45813c61732?q=80&w=800&auto=format&fit=crop"
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
                   class="mt-auto bg-[#0FA9A0] hover:bg-[#0d928a] text-white px-8 py-3 rounded-full font-medium transition-all duration-300 flex items-center gap-2 shadow-md shadow-[#0fa9a0]/20 hover:shadow-lg hover:shadow-[#0fa9a0]/30 w-max mx-auto hover:-translate-y-0.5 text-[14px]">
                    Explore <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <!-- Card 2: Musical Application -->
            <div class="bg-white rounded-3xl p-6 pb-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative flex flex-col items-center">

                <!-- Icon Badge -->
                <div class="absolute -top-9 bg-[#0FA9A0] text-white w-[68px] h-[68px] rounded-full flex items-center justify-center border-[5px] border-[#F8FAFC] shadow-sm z-10">
                    <i class="fa-solid fa-music text-[26px]"></i>
                </div>

                <!-- Image -->
                <div class="w-full h-52 rounded-2xl overflow-hidden mt-8 mb-6 shadow-sm bg-black">
                    <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=800&auto=format&fit=crop"
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
                   class="mt-auto bg-[#0FA9A0] hover:bg-[#0d928a] text-white px-8 py-3 rounded-full font-medium transition-all duration-300 flex items-center gap-2 shadow-md shadow-[#0fa9a0]/20 hover:shadow-lg hover:shadow-[#0fa9a0]/30 w-max mx-auto hover:-translate-y-0.5 text-[14px]">
                    Explore <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection