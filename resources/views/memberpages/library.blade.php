@extends('layouts.member')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-8">
  
  {{-- Header --}}
  <div class="mb-8">
    <span class="inline-block bg-indigo-50 text-indigo-700 text-xs font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full mb-3">
      Resource Hub
    </span>
    <h1 class="text-3xl font-black text-gray-900 leading-tight">Gospel Keys Library</h1>
    <p class="text-sm text-gray-500 mt-1.5">Access all your exercises, ear training modules, extra courses, and learn song libraries in one central place.</p>
  </div>

  {{-- Library Grid --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    {{-- Piano Exercises --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
      <div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-5 shadow-sm">
          🎹
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Piano Exercises</h3>
        <p class="text-xs text-gray-400 font-semibold mb-4 uppercase tracking-wide">
          {{ $pianoExerciseCount }} {{ Str::plural('exercise', $pianoExerciseCount) }} available
        </p>
        <p class="text-sm text-gray-500 leading-relaxed mb-6">
          Build finger speed, dexterity, hand independence, and technical agility on the keyboard.
        </p>
      </div>
      <a href="/member/piano-exercise" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
        Open Exercises
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
      </a>
    </div>

    {{-- Ear Training --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
      <div>
        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl mb-5 shadow-sm">
          👂
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Ear Training</h3>
        <p class="text-xs text-gray-400 font-semibold mb-4 uppercase tracking-wide">
          {{ $earTrainingCount }} {{ Str::plural('module', $earTrainingCount) }} available
        </p>
        <p class="text-sm text-gray-500 leading-relaxed mb-6">
          Train your ear to recognize chord progressions, vocal movements, and melody lines.
        </p>
      </div>
      <a href="/member/ear-training" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold transition shadow-sm">
        Open Ear Training
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
      </a>
    </div>

    {{-- Extra Courses --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
      <div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-5 shadow-sm">
          📚
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Extra Courses</h3>
        <p class="text-xs text-gray-400 font-semibold mb-4 uppercase tracking-wide">
          {{ $extraCoursesCount }} {{ Str::plural('course', $extraCoursesCount) }} available
        </p>
        <p class="text-sm text-gray-500 leading-relaxed mb-6">
          Deep-dive into special topics like gospel chords, passing chords, and music theory.
        </p>
      </div>
      <a href="/member/extra-courses" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
        Open Extra Courses
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
      </a>
    </div>

    {{-- Learn Songs --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition duration-300">
      <div>
        <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-2xl mb-5 shadow-sm">
          🎵
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Learn Songs</h3>
        <p class="text-xs text-gray-400 font-semibold mb-4 uppercase tracking-wide">
          {{ $learnSongsCount }} {{ Str::plural('song', $learnSongsCount) }} available
        </p>
        <p class="text-sm text-gray-500 leading-relaxed mb-6">
          Learn how to play popular worship and gospel songs step-by-step with details.
        </p>
      </div>
      <a href="/member/learn-songs" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
        Open Learn Songs
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
      </a>
    </div>

  </div>

</section>
@endsection
