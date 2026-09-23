@php
    $noHeader = true;
@endphp
@extends('layouts.member')

@section('content')

<div
  x-data="{ step: {{ request('step', 1) }}, totalSteps: 4, showDiscoveryCallModal: false }"
  @open-discovery-call-modal.window="showDiscoveryCallModal = true"
  x-init="$watch('step', () => window.scrollTo({ top: 0, behavior: 'smooth' }))"
  class="min-h-screen bg-white dark:bg-gray-900 py-6 px-4 flex flex-col"
>

  {{-- ── Header ── --}}
  <div class="max-w-5xl mx-auto w-full flex items-start justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">Get Started</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Your journey begins here, <span class="font-semibold text-gray-700 dark:text-gray-300">{{ auth()->user()->first_name }}</span></p>
    </div>
    <a href="/member/dashboard" class="flex items-center space-x-1.5 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <span>Exit Setup</span>
    </a>
  </div>

  {{-- ── Step Progress Bar ── --}}
  @php
    $stepLabels = [1 => 'Take a Tour', 2 => 'Find Best Path', 3 => 'Select a Course', 4 => 'Join Community'];
  @endphp
  <div class="max-w-5xl mx-auto mt-6 mb-6 w-full border border-gray-100 dark:border-gray-800 rounded-2xl bg-white dark:bg-gray-900 shadow-sm p-4 sm:p-6">
    <div class="flex items-start w-full">
      @for ($i = 1; $i <= 4; $i++)
        <div class="flex flex-col items-center flex-shrink-0">
          {{-- Step circle --}}
          <div
            class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-all duration-300"
            :class="{
              'bg-[#1447A6] border-[#1447A6] text-white': step >= {{ $i }},
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
          <span
            class="hidden sm:block mt-2 text-xs font-medium whitespace-nowrap"
            :class="step >= {{ $i }} ? 'text-[#1447A6]' : 'text-gray-400'"
          >{{ $stepLabels[$i] }}</span>
        </div>
        {{-- Connector line (skip after last) --}}
        @if ($i < 4)
          <div class="flex-1 h-px mx-2 mt-[18px]"
            :class="step > {{ $i }} ? 'bg-[#1447A6]' : 'bg-gray-200'"
          ></div>
        @endif
      @endfor
    </div>
  </div>

  {{-- ── Step Cards ── --}}
  <div class="max-w-5xl mx-auto w-full flex-1">

  {{-- STEP 1 --}}
  <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm p-8">

      {{-- Header: left-aligned heading + description --}}
      <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight leading-tight mb-1">
          Take a tour of the website
        </h2>
        <p class="text-gray-500 text-sm leading-relaxed">
          A quick walkthrough of the platform, so you know exactly where everything is.
        </p>
      </div>

      {{-- Video --}}
      <div class="overflow-hidden rounded-xl shadow-lg shadow-gray-300/40 ring-1 ring-black/5">
        <script src="https://fast.wistia.com/player.js" async></script>
        <script src="https://fast.wistia.com/embed/gd8m2mxi65.js" async type="module"></script>
        <style>
          wistia-player[media-id='gd8m2mxi65']:not(:defined) {
            background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gd8m2mxi65/swatch');
            display: block;
            filter: blur(5px);
            padding-top: 56.25%;
          }
        </style>
        <wistia-player media-id="gd8m2mxi65" aspect="1.7777777777777777" class="w-full"></wistia-player>
      </div>

    </div>
  </div>

  {{-- STEP 2 --}}
  <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm p-8">

    {{-- Heading --}}
    <div class="mb-6">
      <h2 class="text-xl font-bold text-gray-900 tracking-tight leading-tight mb-1">Find Your Best Path</h2>
      <p class="text-gray-500 text-sm leading-relaxed">{{ auth()->user()->premium ? 'Take a quick assessment or get a personalized roadmap — whichever fits you best.' : 'Take a quick assessment to find the path that fits you best.' }}</p>
    </div>

    {{-- Two-card choice row --}}
    <div class="flex flex-col md:flex-row items-stretch gap-4">

      {{-- Card 1: Discover Your Level --}}
      <div class="flex-1 bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex flex-col {{ auth()->user()->premium ? '' : 'md:max-w-[calc(50%-0.5rem)]' }}">

        {{-- Icon --}}
        <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
        </div>

        <h3 class="text-base font-bold text-gray-900 mb-3">Discover your level</h3>

        <div class="border-t border-gray-100 mb-3"></div>

        {{-- Features --}}
        <ul class="divide-y divide-gray-100 mb-5 w-full text-left flex-1">
          <li class="flex items-center gap-3 py-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">Takes less than 2 minutes</span>
          </li>
          <li class="flex items-center gap-3 py-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1447A6]" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">Instant results</span>
          </li>
          <li class="flex items-center gap-3 py-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
              </svg>
            </div>
            <span class="text-sm text-gray-700">Skill Evaluation</span>
          </li>
        </ul>

        <a href="/member/quiz" class="w-full flex items-center justify-center border border-[#1447A6] text-[#1447A6] hover:bg-blue-50 font-semibold py-2.5 rounded-lg transition text-sm">
          Start Assessment
        </a>
      </div>

      @if(auth()->user()->premium)
      {{-- Card 2: Personalized Guidance --}}
      <div id="personalized-guidance-card" class="flex-1 flex" data-guidance-url="{{ route('member.personalized-guidance.create') }}"></div>
      @endif

    </div>
    </div>
  </div>

  {{-- STEP 3 --}}
  <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm p-8">

      <h2 class="text-xl font-bold text-gray-900 mb-1">Start a Course Based on Your Skill Level</h2>
      <p class="text-sm text-gray-500 leading-relaxed mb-6">
        Choose the path that matches your current knowledge and experience.<br class="hidden sm:block">
        You can always switch levels later.
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Beginner --}}
        <div class="border border-gray-200 rounded-2xl p-4 flex flex-col hover:shadow-md transition">
          <div class="rounded-xl overflow-hidden bg-gray-100 mb-4">
            <img src="/images/featured1.jpeg" alt="Beginner" class="w-full h-32 object-cover">
          </div>
          <p class="text-base font-bold text-gray-900 mb-1">Beginner Course</p>
          <p class="text-xs text-gray-500 mb-4 leading-relaxed">New to the piano? Build a solid foundation in scales, chords, and theory at a pace that actually sticks.</p>
          <a href="/member/course/beginner" class="mt-auto w-full flex items-center justify-center space-x-1 border border-[#1447A6] text-[#1447A6] rounded-xl py-2.5 text-sm font-semibold transition">
            <span>Start Course</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </a>
        </div>

        {{-- Intermediate --}}
        <div class="border border-gray-200 rounded-2xl p-4 flex flex-col hover:shadow-md transition">
          <div class="rounded-xl overflow-hidden bg-gray-100 mb-4">
            <img src="/images/featured2.jpeg" alt="Intermediate" class="w-full h-32 object-cover">
          </div>
          <p class="text-base font-bold text-gray-900 mb-1">Intermediate Course</p>
          <p class="text-xs text-gray-500 mb-4 leading-relaxed">Already comfortable with the basics? Sharpen your chord vocabulary and start playing with more depth and feel.</p>
          <a href="/member/course/intermediate" class="mt-auto w-full flex items-center justify-center space-x-1 border border-green-600 text-green-600 hover:bg-green-600 hover:text-white rounded-xl py-2.5 text-sm font-semibold transition">
            <span>Start Course</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </a>
        </div>

        {{-- Advanced --}}
        <div class="border border-gray-200 rounded-2xl p-4 flex flex-col hover:shadow-md transition">
          <div class="rounded-xl overflow-hidden bg-gray-100 mb-4">
            <img src="/images/featured3.jpeg" alt="Advanced" class="w-full h-32 object-cover">
          </div>
          <p class="text-base font-bold text-gray-900 mb-1">Advanced Course</p>
          <p class="text-xs text-gray-500 mb-4 leading-relaxed">Ready to push further? Master advanced voicings, substitutions, and improvisation like a seasoned player.</p>
          <a href="/member/course/advanced" class="mt-auto w-full flex items-center justify-center space-x-1 border border-[#C85A5A] text-[#C85A5A] hover:bg-[#C85A5A] hover:text-white rounded-xl py-2.5 text-sm font-semibold transition">
            <span>Start Course</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </a>
        </div>

      </div>

      <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mt-6 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">
        <div class="flex items-start space-x-3">
          <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-800">Not sure which level to choose?</p>
            <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">You can take our quick assessment to find the best level for you.</p>
          </div>
        </div>
        <a href="/member/quiz" class="inline-flex items-center flex-shrink-0 text-[#1447A6] text-sm font-semibold hover:underline">
          Take Assessment
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      </div>

    </div>
  </div>

  {{-- STEP 4 --}}
  <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
    <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm p-8">

      <h2 class="text-xl font-bold text-gray-900 mb-1">Participate in the Community</h2>
      <p class="text-sm text-gray-500 leading-relaxed mb-6">
        Connect with fellow musicians, share your progress, get feedback, and stay motivated on your journey.
      </p>

      <div class="flex flex-col items-center text-center py-4">

        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-5">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>

        <h3 class="text-2xl font-extrabold text-gray-900 mb-7">Join Our Community</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8 w-full max-w-xl">
          @foreach([
            ['label'=>'Get Help', 'desc'=>'Ask questions and get support from the community', 'icon'=>'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M5.636 5.636l3.536 3.536m0 5.656l-3.536 3.536M12 12m-3 0a3 3 0 106 0 3 3 0 10-6 0'],
            ['label'=>'Share Progress', 'desc'=>'Post your music and celebrate wins', 'icon'=>'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3'],
            ['label'=>'Stay Inspired', 'desc'=>'See what others are learning and creating', 'icon'=>'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z'],
            ['label'=>'Build Connections', 'desc'=>'Network with like-minded musicians', 'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
          ] as $item)
          <div class="flex items-start gap-3 bg-blue-50/60 border border-blue-100 rounded-xl p-4 text-left">
            <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 text-[#1447A6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
              </svg>
            </div>
            <div>
              <p class="text-sm font-bold text-gray-900">{{ $item['label'] }}</p>
              <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $item['desc'] }}</p>
            </div>
          </div>
          @endforeach
        </div>

        <a href="{{ route('community.activity-feed') }}" class="w-full max-w-sm flex items-center justify-center space-x-2 bg-[#1447A6] hover:bg-[#0F3A8A] text-white font-semibold py-3.5 rounded-xl transition text-sm shadow-sm shadow-blue-200">
          <span>Join Community</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>

      </div>

    </div>
  </div>

  </div>{{-- end step cards wrapper --}}

  {{-- ── Footer Navigation ── --}}
  <div class="max-w-5xl mx-auto w-full mt-auto border-t border-gray-100 pt-4 flex items-center justify-between gap-3 px-4 sm:px-0">

    <button
      @click="if (step > 1) step--"
      :disabled="step === 1"
      class="flex items-center gap-1.5 px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed whitespace-nowrap"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
      <span>Previous</span>
    </button>

    <button
      @click="if (step < totalSteps) { step++ } else { window.location.href = '{{ route('home') }}' }"
      class="flex items-center gap-1.5 px-4 py-2.5 bg-[#1447A6] text-white rounded-lg text-sm font-medium hover:bg-[#0F3A8A] transition whitespace-nowrap"
    >
      <span x-text="step === totalSteps ? 'Finish' : 'Next Step'"></span>
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>
    </button>

  </div>

  {{-- ── Discovery Call Modal ── --}}
  <div
    x-cloak
    x-show="showDiscoveryCallModal"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
  >
    <!-- Background backdrop -->
    <div
      x-show="showDiscoveryCallModal"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
      @click="showDiscoveryCallModal = false"
    ></div>

    <!-- Modal position wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
      <div
        x-show="showDiscoveryCallModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative transform overflow-hidden rounded-[24px] bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-4xl border border-gray-100 flex flex-col"
      >
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
          <div class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900" id="modal-title">
              Book Discovery Call
            </h3>
          </div>
          <button
            @click="showDiscoveryCallModal = false"
            type="button"
            class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
          >
            <span class="sr-only">Close modal</span>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body (Iframe) -->
        <div class="bg-white p-2 sm:p-4">
          <iframe
            src="https://calendar.google.com/calendar/appointments/schedules/AcZssZ0VKbR_cb5DfipW_nRZiGtwsXkBlbwwG8q4kutzKRqaVO9-AdBCzb3ltzCS3BqotzPnKRCIGpoV?gv=true"
            style="border: 0"
            width="100%"
            height="600"
            frameborder="0"
            class="rounded-xl overflow-hidden"
          ></iframe>
        </div>
      </div>
    </div>
  </div>

</div>

@endsection
