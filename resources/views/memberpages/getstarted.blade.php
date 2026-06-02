@extends('layouts.member')

@section('content')

<div
  x-data="{ step: 1, totalSteps: 4 }"
  class="min-h-screen bg-white dark:bg-gray-900 py-6 px-4"
>

  {{-- ── Header ── --}}
  <div class="max-w-5xl mx-auto mb-6 flex items-start justify-between">
    <div class="flex items-start space-x-3">
      <div class="text-indigo-600 mt-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
      </div>
      <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">Get Started</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Follow these simple steps to get the most out of your learning experience.</p>
      </div>
    </div>
    <a href="/member/dashboard" class="flex items-center space-x-1.5 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <span>Exit Tour</span>
    </a>
  </div>

  {{-- ── Step Progress Bar ── --}}
  <div class="max-w-5xl mx-auto mb-8">
    <div class="flex items-center justify-center">
      @for ($i = 1; $i <= 4; $i++)
        {{-- Step circle --}}
        <div
          class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-all duration-300 flex-shrink-0"
          :class="{
            'bg-indigo-600 border-indigo-600 text-white': step === {{ $i }},
            'bg-indigo-600 border-indigo-600 text-white': step > {{ $i }},
            'bg-white border-gray-300 text-gray-400': step < {{ $i }}
          }"
        >
          <span x-show="step <= {{ $i }}">{{ $i }}</span>
          <span x-show="step > {{ $i }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </span>
        </div>
        {{-- Connector line (skip after last) --}}
        @if ($i < 4)
          <div class="flex-1 h-px mx-2"
            :class="step > {{ $i }} ? 'bg-indigo-600' : 'bg-gray-200'"
          ></div>
        @endif
      @endfor
    </div>
  </div>

  {{-- ── Step Cards ── --}}
  <div class="max-w-5xl mx-auto" style="min-height:520px;">

  {{-- STEP 1 --}}
  <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="flex flex-col md:flex-row" style="min-height:520px;">

        {{-- Left Panel --}}
        <div class="w-full md:w-2/5 p-8 flex flex-col justify-between">
          <div>
            <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-5">
              Step 1 of 4
            </span>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white leading-tight mb-3">
              Step 1: Take a tour of the website.
            </h2>
            <div class="w-10 h-1 bg-indigo-600 rounded mb-5"></div>
            <p class="text-gray-500 text-sm leading-relaxed mb-8">
              Watch this quick tour to get familiar with the platform and discover everything that's available to help you grow as a musician.
            </p>
          </div>

          <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-start space-x-3">
            <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">Why take the tour?</p>
              <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">It only takes a few minutes and helps you save time and get started with confidence.</p>
            </div>
          </div>
        </div>

        {{-- Right Panel – Video --}}
        <div class="w-full md:w-3/5 bg-white flex items-center justify-center min-h-[340px]">
          <iframe
            src="https://player.vimeo.com/video/1195123553"
            style="border:0; max-width:100%;"
            class="w-full h-[420px]"
            allowfullscreen
            allow="autoplay; fullscreen; picture-in-picture"
          ></iframe>
        </div>

      </div>
    </div>
  </div>

  {{-- STEP 2 --}}
  <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="flex flex-col md:flex-row" style="min-height:520px;">

        {{-- Left Panel – Music Snapshot --}}
        <div class="w-full md:w-2/5 border-r border-gray-100 p-7 flex flex-col">
          <div class="flex items-center space-x-2 mb-6">
            <span class="bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded">YOUR</span>
            <span class="text-gray-500 text-xs font-bold tracking-widest uppercase">Music Snapshot</span>
          </div>

          {{-- Circular Progress --}}
          <div class="flex flex-col items-center mb-6">
            <div class="relative w-36 h-36">
              <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="42" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                <circle cx="50" cy="50" r="42" fill="none" stroke="#6d28d9" stroke-width="8"
                  stroke-dasharray="263.9" stroke-dashoffset="71.3" stroke-linecap="round"/>
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-extrabold text-gray-900">72</span>
                <span class="text-xs text-gray-400">/100</span>
              </div>
            </div>
            <p class="text-indigo-600 font-bold text-lg mt-3">Growing Musician</p>
            <p class="text-gray-400 text-sm">You're building a strong foundation!</p>
          </div>

          {{-- Skill Bars --}}
          <div class="space-y-3">
            @foreach([['icon'=>'📖','label'=>'Fundamentals','val'=>75],['icon'=>'👂','label'=>'Ear Training','val'=>68],['icon'=>'🎹','label'=>'Chords and Harmony','val'=>80],['icon'=>'🌟','label'=>'Experience','val'=>70]] as $skill)
            <div class="flex items-center space-x-2">
              <span class="text-base w-5 text-center">{{ $skill['icon'] }}</span>
              <span class="text-xs text-gray-600 w-24 flex-shrink-0">{{ $skill['label'] }}</span>
              <div class="flex-1 bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width:{{ $skill['val'] }}%"></div>
              </div>
              <span class="text-xs text-gray-500 w-12 text-right">{{ $skill['val'] }}/100</span>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Right Panel – CTA --}}
        <div class="w-full md:w-3/5 p-8 flex flex-col justify-center relative overflow-hidden">

          {{-- Decorative clipboard illustration (CSS-only) --}}
          <div class="absolute top-4 right-4 opacity-10 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-40 h-40 text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
              <path d="M9 2a1 1 0 000 2h6a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h6a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h6a1 1 0 100-2H7zm0 4a1 1 0 100 2h4a1 1 0 100-2H7z" clip-rule="evenodd"/>
            </svg>
          </div>

          <p class="text-orange-500 text-xs font-bold uppercase tracking-widest mb-3">Discover Where You Stand</p>
          <h2 class="text-3xl font-extrabold text-gray-900 leading-tight mb-4">Step 2: Find Your Best Path</h2>
          <p class="text-gray-500 text-sm leading-relaxed mb-8">
            Take a quick assessment to discover your strengths, highlight areas to grow, and get a personalized learning path tailored just for you.
          </p>

          {{-- Feature badges --}}
          <div class="flex flex-col sm:flex-row gap-3 mb-8">
            <div class="flex items-center space-x-2 bg-purple-50 rounded-xl px-4 py-3 flex-1">
              <span class="text-xl">⏱️</span>
              <div>
                <p class="text-xs font-bold text-gray-800">15 Quick</p>
                <p class="text-xs text-gray-500">Questions</p>
              </div>
            </div>
            <div class="flex items-center space-x-2 bg-orange-50 rounded-xl px-4 py-3 flex-1">
              <span class="text-xl">📊</span>
              <div>
                <p class="text-xs font-bold text-gray-800">Instant</p>
                <p class="text-xs text-gray-500">Results</p>
              </div>
            </div>
            <div class="flex items-center space-x-2 bg-green-50 rounded-xl px-4 py-3 flex-1">
              <span class="text-xl">⭐</span>
              <div>
                <p class="text-xs font-bold text-gray-800">Personalized</p>
                <p class="text-xs text-gray-500">Growth Plan</p>
              </div>
            </div>
          </div>

          {{-- CTA Button --}}
          <a href="/member/quiz" class="w-full flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-xl transition text-base mb-3">
            <span>Take My Assessment</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
          <p class="text-center text-xs text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            100% Free &bull; Takes Less Than 2 Minutes
          </p>
        </div>

      </div>
    </div>
  </div>

  {{-- STEP 3 --}}
  <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="flex flex-col md:flex-row" style="min-height:520px;">

        {{-- Left Panel --}}
        <div class="w-full md:w-[30%] border-r border-gray-100 p-8 flex flex-col justify-between">
          <div>
            <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-5">
              Step 3 of 4
            </span>
            <h2 class="text-2xl font-extrabold text-gray-900 leading-tight mb-3">
              Step 3: Start a Course Based on Your Skill Level
            </h2>
            <div class="w-10 h-1 bg-indigo-600 rounded mb-5"></div>
            <p class="text-gray-500 text-sm leading-relaxed">
              Choose the path that matches your current knowledge and experience. You can always switch levels later.
            </p>
          </div>

          <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mt-8">
            <div class="flex items-start space-x-3 mb-3">
              <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800">Not sure which level to choose?</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">You can take our quick assessment to find the best level for you.</p>
              </div>
            </div>
            <a href="/member/quiz" class="inline-flex items-center text-indigo-600 text-sm font-semibold hover:underline">
              Take Assessment
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
              </svg>
            </a>
          </div>
        </div>

        {{-- Right Panel – Course Cards --}}
        <div class="w-full md:w-[70%] p-8">
          <h3 class="text-lg font-bold text-gray-900 mb-1">Select Your Starting Point</h3>
          <p class="text-sm text-gray-500 mb-6">Pick the level that best describes your current skills.</p>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Beginner --}}
            <div class="border border-gray-200 rounded-2xl p-5 flex flex-col items-center text-center hover:shadow-md transition">
              <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
              </div>
              <p class="text-indigo-600 font-bold text-base mb-4">Beginner</p>
              <ul class="text-left space-y-1.5 mb-6 w-full">
                @foreach(['Music theory','Scales and Chords','Harmonic Tension'] as $item)
                <li class="flex items-center text-xs text-gray-600 space-x-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span>{{ $item }}</span>
                </li>
                @endforeach
              </ul>
              <a href="/member/course/beginner" class="mt-auto w-full flex items-center justify-center space-x-1 border border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl py-2.5 text-sm font-semibold transition">
                <span>Start Course</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              </a>
            </div>

            {{-- Intermediate --}}
            <div class="border border-gray-200 rounded-2xl p-5 flex flex-col items-center text-center hover:shadow-md transition">
              <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <p class="text-green-600 font-bold text-base mb-4">Intermediate</p>
              <ul class="text-left space-y-1.5 mb-6 w-full">
                @foreach(['Chord Extensions','Passing Tones','Embellishments'] as $item)
                <li class="flex items-center text-xs text-gray-600 space-x-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span>{{ $item }}</span>
                </li>
                @endforeach
              </ul>
              <a href="/member/course/intermediate" class="mt-auto w-full flex items-center justify-center space-x-1 border border-green-600 text-green-600 hover:bg-green-600 hover:text-white rounded-xl py-2.5 text-sm font-semibold transition">
                <span>Start Course</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              </a>
            </div>

            {{-- Advanced --}}
            <div class="border border-gray-200 rounded-2xl p-5 flex flex-col items-center text-center hover:shadow-md transition">
              <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
              </div>
              <p class="text-purple-600 font-bold text-base mb-4">Advanced</p>
              <ul class="text-left space-y-1.5 mb-6 w-full">
                @foreach(['Substitutions','Advanced Voicings','Improvisation'] as $item)
                <li class="flex items-center text-xs text-gray-600 space-x-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  <span>{{ $item }}</span>
                </li>
                @endforeach
              </ul>
              <a href="/member/course/advanced" class="mt-auto w-full flex items-center justify-center space-x-1 border border-purple-600 text-purple-600 hover:bg-purple-600 hover:text-white rounded-xl py-2.5 text-sm font-semibold transition">
                <span>Start Course</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              </a>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- STEP 4 --}}
  <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="flex flex-col md:flex-row" style="min-height:520px;">

        {{-- Left Panel --}}
        <div class="w-full md:w-[38%] border-r border-gray-100 p-8 flex flex-col justify-between">
          <div>
            <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-5">
              Step 4 of 4
            </span>
            <h2 class="text-2xl font-extrabold text-gray-900 leading-tight mb-3">
              Step 4: Participate in the Community
            </h2>
            <div class="w-10 h-1 bg-indigo-600 rounded mb-5"></div>
            <p class="text-gray-500 text-sm leading-relaxed">
              Connect with fellow musicians, share your progress, get feedback, and stay motivated on your journey.
            </p>
          </div>

          <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-start space-x-3 mt-8">
            <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-800">You're not alone in this journey</p>
              <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">Join thousands of musicians who are learning, sharing, and growing together.</p>
            </div>
          </div>
        </div>

        {{-- Right Panel --}}
        <div class="w-full md:w-[62%] p-10 flex flex-col items-center justify-center text-center">

          <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>

          <h3 class="text-2xl font-extrabold text-gray-900 mb-7">Join Our Community</h3>

          <ul class="text-left space-y-3 mb-8 w-full max-w-sm">
            @foreach([
              ['label'=>'Get Help:', 'desc'=>'Ask questions and get support from the community'],
              ['label'=>'Share Progress:', 'desc'=>'Post your music and celebrate wins'],
              ['label'=>'Stay Inspired:', 'desc'=>'See what others are learning and creating'],
              ['label'=>'Build Connections:', 'desc'=>'Network with like-minded musicians'],
            ] as $item)
            <li class="flex items-start space-x-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <span class="text-sm text-gray-700"><span class="font-semibold">{{ $item['label'] }}</span> {{ $item['desc'] }}</span>
            </li>
            @endforeach
          </ul>

          <a href="/member/community" class="w-full max-w-sm flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3.5 rounded-xl transition text-sm mb-3">
            <span>Join Community</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
          </a>

        </div>

      </div>
    </div>
  </div>

  </div>{{-- end step cards wrapper --}}

  {{-- ── Footer Navigation ── --}}
  <div class="max-w-5xl mx-auto mt-8 flex items-center justify-between">

    <button
      @click="if (step > 1) step--"
      :disabled="step === 1"
      class="flex items-center space-x-2 px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
      <span>Previous</span>
    </button>

    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
      Step <span x-text="step"></span> of <span x-text="totalSteps"></span>
    </span>

    <button
      @click="if (step < totalSteps) step++"
      :disabled="step === totalSteps"
      class="flex items-center space-x-2 px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition disabled:opacity-40 disabled:cursor-not-allowed"
    >
      <span x-text="step === totalSteps ? 'Finish' : 'Next Step'"></span>
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>
    </button>

  </div>

</div>

@endsection
