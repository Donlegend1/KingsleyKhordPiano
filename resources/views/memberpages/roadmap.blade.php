@extends('layouts.member')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="/home" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="/member/roadmap" class="text-[#6366F1] font-medium">Roadmap</a>
  </div>

  {{-- Title --}}
  <div class="mb-10">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-1">Your Learning Roadmap</h1>
    <div class="w-10 h-1 bg-[#6366F1] rounded mb-3"></div>
    <p class="text-gray-500 text-sm">Choose your path and start building the skills that will take your musicianship to the next level.</p>
  </div>

  {{-- Cards --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Card 1: Beginner --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
      <div class="flex items-center justify-center pt-8 pb-4 bg-[#F5F3FF] min-h-[180px]">
        <img src="/images/featured1.jpeg" alt="Beginner" class="h-36 w-auto object-contain drop-shadow-md">
      </div>
      <div class="p-6 flex flex-col flex-1">
        <h2 class="text-xl font-extrabold text-gray-900 mb-2">Build Your Foundation</h2>
        <p class="text-gray-500 text-sm mb-5">Start your journey by mastering the basics and building a strong foundation.</p>
        <ul class="space-y-2 mb-6">
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#6366F1] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Music theory
          </li>
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#6366F1] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Scales and Chords
          </li>
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#6366F1] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Harmonic Tension
          </li>
        </ul>
        <div class="mt-auto">
          <a href="/member/course/beginner"
             class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#6366F1] hover:bg-[#4F46E5] text-white font-semibold rounded-xl transition">
            Start Course
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
      </div>
    </div>

    {{-- Card 2: Intermediate --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
      <div class="flex items-center justify-center pt-8 pb-4 bg-[#F0FDF4] min-h-[180px]">
        <img src="/images/featured2.jpeg" alt="Intermediate" class="h-36 w-auto object-contain drop-shadow-md">
      </div>
      <div class="p-6 flex flex-col flex-1">
        <h2 class="text-xl font-extrabold text-gray-900 mb-2">Elevate Your Playing</h2>
        <p class="text-gray-500 text-sm mb-5">Improve the quality of your chords and learn how to harmonize with confidence.</p>
        <ul class="space-y-2 mb-6">
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#22C55E] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Chord Extensions
          </li>
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#22C55E] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Passing Tones
          </li>
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#22C55E] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Embellishments
          </li>
        </ul>
        <div class="mt-auto">
          <a href="/member/course/intermediate"
             class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#22C55E] hover:bg-[#16A34A] text-white font-semibold rounded-xl transition">
            Start Course
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
      </div>
    </div>

    {{-- Card 3: Advanced --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
      <div class="flex items-center justify-center pt-8 pb-4 bg-[#FFF5F5] min-h-[180px]">
        <img src="/images/featured3.jpeg" alt="Advanced" class="h-36 w-auto object-contain drop-shadow-md">
      </div>
      <div class="p-6 flex flex-col flex-1">
        <h2 class="text-xl font-extrabold text-gray-900 mb-2">Master & Create</h2>
        <p class="text-gray-500 text-sm mb-5">Think like a musician, create freely and lead with excellence.</p>
        <ul class="space-y-2 mb-6">
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#EF4444] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Substitutions
          </li>
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#EF4444] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Advanced Voicings
          </li>
          <li class="flex items-center gap-2 text-sm text-gray-700">
            <svg class="w-5 h-5 text-[#EF4444] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            Improvisation
          </li>
        </ul>
        <div class="mt-auto">
          <a href="/member/course/advanced"
             class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#EF4444] hover:bg-[#DC2626] text-white font-semibold rounded-xl transition">
            Start Course
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
