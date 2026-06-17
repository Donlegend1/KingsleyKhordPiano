@extends('layouts.member')

@php
  $noHeader = true;
  $noFooter = true;

  $score = $assessment->score;
  $level = $assessment->skill_level; // 'Beginner', 'Intermediate', 'Advanced'

  // Dynamic values based on level
  if ($level === 'Advanced') {
      $themeColor = 'red';
      $levelColorClass = 'text-red-600';
      $levelBgClass = 'bg-red-50';
      $levelDescription = 'You demonstrate strong musical proficiency and a deep understanding of gospel music. Continue refining your artistry, mastering advanced techniques, and developing your unique musical voice.';
      $levelIcon = '🔥';
  } elseif ($level === 'Intermediate') {
      $themeColor = 'violet';
      $levelColorClass = 'text-violet-600';
      $levelBgClass = 'bg-violet-50';
      $levelDescription = 'You have a solid foundation and good understanding of gospel music. Keep building consistency and explore more advanced concepts!';
      $levelIcon = '📊';
  } else {
      $themeColor = 'blue';
      $levelColorClass = 'text-blue-600';
      $levelBgClass = 'bg-blue-50';
      $levelDescription = 'You’re building the fundamentals of your musical journey. Focus on developing strong basics, steady practice habits, and confidence in your playing. Every great musician starts here!';
      $levelIcon = '🌱';
  }

  // Rating helper function
  function getRatingText($percent) {
      if ($percent >= 75) return 'Strong';
      if ($percent >= 40) return 'Good';
      return 'Working on it';
  }
@endphp

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col font-sans">

  {{-- ── Custom Quiz Header ── --}}
  <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-50">
    <div class="flex items-center gap-3">
      <a href="/" class="flex items-center gap-2">
        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-sm">
          KK
        </div>
        <span class="font-extrabold text-gray-900 text-lg tracking-tight">Gospel Keys</span>
      </a>
      <span class="text-gray-300 font-light text-xl">|</span>
      <span class="text-sm font-semibold text-gray-500">Gospel Musician Skills Assessment</span>
    </div>

    <div class="flex items-center gap-4">
      <a href="{{ route('member.quiz', ['retake' => 1]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-indigo-200 text-indigo-600 bg-white hover:bg-indigo-50 rounded-xl text-xs font-bold transition shadow-sm">
        <svg class="w-3.5 h-3.5 fill-none stroke-current stroke-width-2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
        </svg>
        Retake Quiz
      </a>
      @if(Auth::user()->passport)
        <img src="{{ Auth::user()->passport }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100">
      @else
        <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
          {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
        </div>
      @endif
    </div>
  </header>

  {{-- ── Main Results Container ── --}}
  <div class="flex-grow max-w-6xl w-full mx-auto px-4 sm:px-6 py-8 flex flex-col gap-6">

    <div>
      <span class="text-xs font-bold text-indigo-500 uppercase tracking-widest">Gospel Musician Skills Assessment</span>
      <h1 class="text-3xl font-extrabold text-gray-900 mt-1 flex items-center gap-2">
        Assessment Complete! 🎉
      </h1>
      <p class="text-sm text-gray-500 mt-1">Great job! Here's a snapshot of your skills and where you stand.</p>
    </div>

    {{-- ── Top Row: Overall Score & Level Card ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 grid grid-cols-1 md:grid-cols-5 gap-8 items-center">
      
      {{-- Gauge --}}
      <div class="md:col-span-2 flex flex-col items-center justify-center md:border-r border-gray-100 md:pr-8">
        <div class="relative w-44 h-44 flex items-center justify-center">
          <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
            <circle class="text-gray-100" stroke-width="8" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"/>
            <circle class="text-emerald-500" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="{{ 251.2 - (251.2 * $score) / 100 }}" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"/>
          </svg>
          <div class="absolute flex flex-col items-center justify-center">
            <span class="text-5xl font-black text-gray-900">{{ $score }}%</span>
            <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">Overall Score</span>
          </div>
        </div>
      </div>

      {{-- Level Description --}}
      <div class="md:col-span-2 flex flex-col gap-3">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Your Current Skill Level</span>
        <h2 class="text-3xl font-black flex items-center gap-2 {{ $levelColorClass }}">
          {{ $level }}
          <span class="text-2xl">{{ $levelIcon }}</span>
        </h2>
        <p class="text-sm text-gray-500 leading-relaxed mt-1">
          {{ $levelDescription }}
        </p>
      </div>

      {{-- Trophy Graphic --}}
      <div class="md:col-span-1 flex items-center justify-center bg-indigo-50/30 rounded-2xl p-6 min-h-[160px] relative overflow-hidden">
        {{-- Background stars decoration --}}
        <div class="absolute top-4 left-4 text-sm opacity-50 animate-bounce">✨</div>
        <div class="absolute bottom-4 right-4 text-sm opacity-50">🎵</div>
        
        {{-- Trophy SVG --}}
        <div class="relative flex flex-col items-center">
          <svg class="w-16 h-16 text-indigo-600 drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v3c0 2.44 1.72 4.48 4 4.9V19H3v2h18v-2h-4v-4.1c2.28-.42 4-2.46 4-4.9V7c0-1.1-.9-2-2-2zM5 10V7h2v3H5zm14 0h-2V7h2v3z"/>
          </svg>
          <div class="w-8 h-8 rounded-full bg-indigo-500 absolute -top-1.5 flex items-center justify-center text-white text-[10px] font-bold shadow-sm">
            ★
          </div>
        </div>
      </div>

    </div>

    {{-- ── Middle Row: Category Breakdown ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col gap-6">
      <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wide">Your Score Breakdown</h3>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Fundamentals --}}
        <div class="border border-gray-100 rounded-xl p-5 flex flex-col justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
              <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Piano</p>
              <h4 class="text-xs font-bold text-gray-800 leading-tight">Fundamentals</h4>
            </div>
          </div>
          <div>
            <div class="flex items-baseline justify-between mb-1.5">
              <span class="text-2xl font-black text-gray-900">{{ $assessment->fundamentals_score }}%</span>
              <span class="text-[10px] font-bold text-purple-500">{{ getRatingText($assessment->fundamentals_score) }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
              <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $assessment->fundamentals_score }}%"></div>
            </div>
          </div>
        </div>

        {{-- Ear Training --}}
        <div class="border border-gray-100 rounded-xl p-5 flex flex-col justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500">
              <svg class="w-5 h-5 fill-none stroke-current stroke-width-2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Ear</p>
              <h4 class="text-xs font-bold text-gray-800 leading-tight">Training</h4>
            </div>
          </div>
          <div>
            <div class="flex items-baseline justify-between mb-1.5">
              <span class="text-2xl font-black text-gray-900">{{ $assessment->ear_training_score }}%</span>
              <span class="text-[10px] font-bold text-orange-500">{{ getRatingText($assessment->ear_training_score) }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
              <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ $assessment->ear_training_score }}%"></div>
            </div>
          </div>
        </div>

        {{-- Chords & Harmony --}}
        <div class="border border-gray-100 rounded-xl p-5 flex flex-col justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
              <svg class="w-5 h-5 fill-none stroke-current stroke-width-2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
              </svg>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Chords &</p>
              <h4 class="text-xs font-bold text-gray-800 leading-tight">Harmony</h4>
            </div>
          </div>
          <div>
            <div class="flex items-baseline justify-between mb-1.5">
              <span class="text-2xl font-black text-gray-900">{{ $assessment->chords_harmony_score }}%</span>
              <span class="text-[10px] font-bold text-emerald-500">{{ getRatingText($assessment->chords_harmony_score) }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
              <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $assessment->chords_harmony_score }}%"></div>
            </div>
          </div>
        </div>

        {{-- Experience & Confidence --}}
        <div class="border border-gray-100 rounded-xl p-5 flex flex-col justify-between gap-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
              <svg class="w-5 h-5 fill-none stroke-current stroke-width-2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.195-.589.81-.989 1.432-.989s1.237.4 1.432.99l1.794 5.419a.75.75 0 00.713.518h5.698c.621 0 1.134.407 1.322.996s-.19 1.206-.69 1.57l-4.609 3.35a.75.75 0 00-.271.847l1.794 5.42c.196.59-.02 1.233-.523 1.596s-1.173.109-1.67-.253L12 18.06l-4.619 3.355c-.497.362-1.168.29-1.67-.252s-.72-1.006-.523-1.597l1.794-5.42a.75.75 0 00-.272-.847L2.1 12.072c-.5-.363-.679-.98-.49-1.57s.7-.996 1.321-.996h5.698a.75.75 0 00.713-.518l1.794-5.42z"/>
              </svg>
            </div>
            <div>
              <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Experience &</p>
              <h4 class="text-xs font-bold text-gray-800 leading-tight">Confidence</h4>
            </div>
          </div>
          <div>
            <div class="flex items-baseline justify-between mb-1.5">
              <span class="text-2xl font-black text-gray-900">{{ $assessment->experience_score }}%</span>
              <span class="text-[10px] font-bold text-blue-500">{{ getRatingText($assessment->experience_score) }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
              <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $assessment->experience_score }}%"></div>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- ── Bottom Row: Quote & Keep Growing CTA ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      {{-- Quote Card --}}
      <div class="bg-[#F8F9FD] border border-[#E9EEFC] rounded-2xl p-8 flex items-center justify-between relative overflow-hidden">
        <div class="flex-grow pr-4 z-10">
          <div class="text-6xl font-serif text-indigo-200 select-none leading-none">“</div>
          <blockquote class="text-base font-extrabold text-gray-800 leading-relaxed -mt-3 italic">
            Skill is built daily. Keep showing up and your sound will grow!
          </blockquote>
        </div>
        <div class="flex-shrink-0 z-10">
          {{-- Mini keyboard illustration using simple CSS --}}
          <div class="w-24 h-16 bg-gray-900 rounded-lg p-1.5 flex flex-col justify-between shadow-md border border-gray-800">
            <div class="flex justify-between h-8 bg-white rounded-sm relative px-0.5">
              @for($k = 0; $k < 10; $k++)
                <div class="w-1.5 h-full bg-gray-100 border-r border-gray-300 flex-shrink-0"></div>
              @endfor
              {{-- Black keys --}}
              <div class="absolute top-0 left-2 w-1 h-5 bg-black"></div>
              <div class="absolute top-0 left-4 w-1 h-5 bg-black"></div>
              <div class="absolute top-0 left-8 w-1 h-5 bg-black"></div>
              <div class="absolute top-0 left-10 w-1 h-5 bg-black"></div>
              <div class="absolute top-0 left-12 w-1 h-5 bg-black"></div>
              <div class="absolute top-0 left-16 w-1 h-5 bg-black"></div>
              <div class="absolute top-0 left-18 w-1 h-5 bg-black"></div>
            </div>
            <div class="flex items-center gap-1.5 text-[8px] font-bold text-indigo-400">
              <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-ping"></span>
              <span>Practicing</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Keep Growing CTA Card --}}
      <div class="bg-emerald-50/20 border border-emerald-100 rounded-2xl p-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4 text-left">
          <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
            🌱
          </div>
          <div>
            <h4 class="text-sm font-extrabold text-gray-900">Keep Growing!</h4>
            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Your journey as a gospel musician is just beginning.</p>
          </div>
        </div>
        <a href="/member/dashboard" class="px-5 py-3 border-2 border-emerald-500 hover:bg-emerald-500 hover:text-white text-emerald-600 rounded-xl text-xs font-bold transition flex items-center gap-2 flex-shrink-0">
          Go to Dashboard
          <svg class="w-3.5 h-3.5 fill-none stroke-current stroke-width-2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
        </a>
      </div>

    </div>

    {{-- ── Privacy note footer ── --}}
    <div class="flex items-center justify-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-4">
      <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
      </svg>
      <span>Your assessment results are private and used only to personalize your learning experience.</span>
    </div>

  </div>

</div>
@endsection
