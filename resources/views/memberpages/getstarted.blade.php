@extends('layouts.member')

@section('content')

<div
  x-data="{ step: 1, totalSteps: 4 }"
  x-init="$watch('step', () => window.scrollTo({ top: 0, behavior: 'smooth' }))"
  class="min-h-screen bg-white dark:bg-gray-900 py-6 px-4 flex flex-col"
>

  {{-- ── Header ── --}}
  <div class="max-w-5xl mx-auto mb-6 w-full flex items-start justify-between">
    <div class="flex items-start space-x-3">
      <div class="text-indigo-600 mt-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
      </div>
      <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">Get Started</h1>
      </div>
    </div>
    <a href="/member/dashboard" class="flex items-center space-x-1.5 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <span>Exit Setup</span>
    </a>
  </div>

  {{-- ── Step Progress Bar ── --}}
  <div class="max-w-5xl mx-auto mb-8 w-full">
    <div class="flex items-center w-full">
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
  <div class="max-w-5xl mx-auto w-full flex-1">

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

    {{-- Step label + heading --}}
    <div class="text-center mb-8">
      <p class="text-indigo-600 text-xs font-bold uppercase tracking-widest mb-2">Step 2 of 4</p>
      <h2 class="text-3xl font-extrabold text-gray-900 leading-tight">Find Your Best Path &amp; Choose Your Skill Level</h2>
    </div>

    {{-- Two-card choice row --}}
    <div class="flex flex-col md:flex-row items-stretch gap-0 relative">

      {{-- Card 1: Discover Your Level --}}
      <div class="flex-1 bg-white border border-gray-200 rounded-2xl shadow-sm p-8 flex flex-col items-center text-center">

        {{-- Icon --}}
        <div class="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-400 -ml-2 mt-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 19V6l12-3v13M9 19a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-6">Discover Your Level</h3>

        {{-- Features --}}
        <ul class="divide-y divide-gray-100 mb-8 w-full text-left">
          <li class="flex items-center space-x-3 py-3">
            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
              {{-- Clock icon – "less than 2 minutes" --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">Takes less than 2 minutes</span>
          </li>
          <li class="flex items-center space-x-3 py-3">
            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
              {{-- Lightning bolt icon – "instant results" --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">Instant results</span>
          </li>
          <li class="flex items-center space-x-3 py-3">
            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
              {{-- Clipboard check icon – "skill evaluation" --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">Skill Evaluation</span>
          </li>
        </ul>

        <a href="/member/quiz" class="mt-auto w-full flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-xl transition text-sm">
          <span>Start Assessment</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      </div>

      {{-- OR divider --}}
      <div class="flex md:flex-col items-center justify-center px-4 py-4 md:py-0 z-10">
        <div class="flex-1 h-px md:h-auto md:w-px bg-gray-200 md:flex-1"></div>
        <span class="mx-3 md:mx-0 md:my-3 w-9 h-9 rounded-full border border-gray-200 bg-white flex items-center justify-center text-xs font-bold text-gray-500 flex-shrink-0 shadow-sm">OR</span>
        <div class="flex-1 h-px md:h-auto md:w-px bg-gray-200 md:flex-1"></div>
      </div>

      {{-- Card 2: Personalized Guidance --}}
      <div class="flex-1 bg-white border border-gray-200 rounded-2xl shadow-sm p-8 flex flex-col items-center text-center relative overflow-hidden">

        {{-- Premium badge --}}
        <div class="absolute top-4 right-4 flex items-center space-x-1 bg-amber-50 border border-amber-200 text-amber-600 text-xs font-bold px-3 py-1 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          <span>Premium Choice</span>
        </div>

        {{-- Icon --}}
        <div class="w-20 h-20 bg-amber-100 rounded-2xl flex items-center justify-center mb-6 mt-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
          </svg>
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-6">Personalized Guidance</h3>

        {{-- Features --}}
        <ul class="divide-y divide-gray-100 mb-8 w-full text-left">
          <li class="flex items-center space-x-3 py-3">
            <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
              {{-- Phone icon – "1-on-1 consultation" --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">1-on-1 consultation</span>
          </li>
          <li class="flex items-center space-x-3 py-3">
            <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
              {{-- Clock icon – "10-minutes call" --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">10-minutes call</span>
          </li>
          <li class="flex items-center space-x-3 py-3">
            <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
              {{-- Map icon – "Personalized Roadmap" --}}
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">Personalized Roadmap</span>
          </li>
        </ul>

        <a href="/member/discovery-call" class="mt-auto w-full flex items-center justify-center space-x-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold py-4 rounded-xl transition text-sm">
          <span>Schedule Discovery Call</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
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
  <div class="max-w-5xl mx-auto w-full mt-auto border-t border-gray-100 pt-6 flex items-center justify-between">

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
