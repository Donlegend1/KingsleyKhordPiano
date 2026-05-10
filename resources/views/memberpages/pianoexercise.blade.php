@extends('layouts.member')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] py-20 px-4 font-sans">
    <div class="max-w-5xl mx-auto">
        <!-- Header Section -->
        {{-- <div class="text-center mb-24">
            <h1 class="text-4xl md:text-[44px] font-bold text-[#1E293B] mb-6 tracking-tight leading-tight">Ready to Take Your Playing<br/>to the Next Level?</h1>
            <p class="text-[#64748B] text-[17px] max-w-2xl mx-auto leading-relaxed">Choose your path to build stronger fingers<br/>and apply your skills musically.</p>
        </div> --}}

        <!-- Cards Container -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-14 max-w-4xl mx-auto">
            
            <!-- Card 1: Finger Exercises -->
            <div class="bg-white rounded-3xl p-6 pb-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative flex flex-col items-center">
                
                <!-- Icon Badge -->
                <div class="absolute -top-10 bg-[#0FA9A0] text-white w-[72px] h-[72px] rounded-full flex items-center justify-center border-[6px] border-white shadow-sm z-10">
                    <i class="fa-solid fa-hand-paper text-[28px]"></i>
                </div>

                <!-- Image -->
                <div class="w-full h-56 rounded-2xl overflow-hidden mt-6 mb-8 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1552422535-c45813c61732?q=80&w=800&auto=format&fit=crop" alt="Finger Exercises" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700">
                </div>

                <!-- Content -->
                <h2 class="text-[22px] font-bold text-[#1E293B] mb-3">Finger Exercises</h2>
                <p class="text-[#64748B] text-center mb-8 px-2 text-[15px] leading-relaxed">Build strength, independence, flexibility,<br>and control with targeted exercises.</p>

                <!-- Button -->
               <a href="{{ route('piano.exercise.finger', ['skill_level' => 'Basic']) }}" class="mt-auto bg-[#0FA9A0] hover:bg-[#0d928a] text-white px-8 py-3.5 rounded-full font-medium transition-all duration-300 flex items-center gap-2 shadow-md shadow-[#0fa9a0]/20 hover:shadow-lg hover:shadow-[#0fa9a0]/40 w-max mx-auto hover:-translate-y-0.5">
                    Explore <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Card 2: Musical Application -->
            <div class="bg-white rounded-3xl p-6 pb-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 relative flex flex-col items-center">
                
                <!-- Icon Badge -->
                <div class="absolute -top-10 bg-[#0FA9A0] text-white w-[72px] h-[72px] rounded-full flex items-center justify-center border-[6px] border-white shadow-sm z-10">
                    <i class="fa-solid fa-music text-[28px]"></i>
                </div>

                <!-- Image -->
                <div class="w-full h-56 rounded-2xl overflow-hidden mt-6 mb-8 shadow-sm bg-black">
                    <!-- Using a dark/magical music notes image to match the mockup -->
                    <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=800&auto=format&fit=crop" alt="Musical Application" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700 opacity-90">
                </div>

                <!-- Content -->
                <h2 class="text-[22px] font-bold text-[#1E293B] mb-3">Musical Application</h2>
                <p class="text-[#64748B] text-center mb-8 px-2 text-[15px] leading-relaxed">Apply your technique in real musical<br>contexts and sound great.</p>

                <!-- Button -->
                <a href="{{ route('piano.exercise.musical') }}" class="mt-auto bg-[#0FA9A0] hover:bg-[#0d928a] text-white px-8 py-3.5 rounded-full font-medium transition-all duration-300 flex items-center gap-2 shadow-md shadow-[#0fa9a0]/20 hover:shadow-lg hover:shadow-[#0fa9a0]/40 w-max mx-auto hover:-translate-y-0.5">
                    Explore <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
