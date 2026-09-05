@extends('layouts.member')

@section('content')

<section class="bg-white text-gray-900 py-4 px-4 border-b border-gray-150">
  <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
    <a href="/home" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-blue-600 font-medium">Audio Quiz</span>
  </div>
</section>

<section class="bg-white py-8 px-6">
  <div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900">Audio Quiz</h1>
    <p class="text-sm text-gray-500 mt-1">Train your ears. Choose a category below to begin.</p>
  </div>
</section>

<section class="bg-[#f4f5f7] py-8 px-6 min-h-screen">
  <div class="max-w-7xl mx-auto">

    @php
      $courses = [
        ['title' => 'Relative Pitch', 'gradient' => 'from-red-500 to-red-900', 'quiz' => $relativePitch],
        ['title' => 'Melodic Dictation', 'gradient' => 'from-yellow-400 to-yellow-700', 'quiz' => $melodicDictation],
        ['title' => 'Intervals', 'gradient' => 'from-green-500 to-green-900', 'quiz' => $intervals],
        ['title' => 'Basic Triads', 'gradient' => 'from-blue-500 to-blue-900', 'quiz' => $basicTriads],
        ['title' => 'Add 9 & b9', 'gradient' => 'from-teal-400 to-teal-800', 'quiz' => $add9],
        ['title' => '7th Degree Chords', 'gradient' => 'from-purple-600 to-purple-900', 'quiz' => $seventhDegree],
        ['title' => 'Secondary 7th Chords', 'gradient' => 'from-fuchsia-600 to-fuchsia-900', 'quiz' => $secondarySeventh],
        ['title' => 'Secondary 9th Chords', 'gradient' => 'from-lime-500 to-lime-800', 'quiz' => $secondaryNinth],
        ['title' => '9th Degree Chords', 'gradient' => 'from-yellow-400 to-amber-700', 'quiz' => $ninthDegree],
        ['title' => '11th Degree Chords', 'gradient' => 'from-cyan-500 to-cyan-900', 'quiz' => $eleventhDegree],
        ['title' => 'Secondary 11th Chords', 'gradient' => 'from-sky-500 to-sky-900', 'quiz' => $secondaryEleventh],
        ['title' => '13th Degree Chords', 'gradient' => 'from-orange-500 to-orange-900', 'quiz' => $thirteenthDegree],
        ['title' => 'Extentions recognition', 'gradient' => 'from-pink-500 to-pink-900', 'quiz' => $extensionsRecognition],
        ['title' => 'Chord Progressions', 'gradient' => 'from-emerald-500 to-emerald-900', 'quiz' => $chordProgressions],
        ['title' => 'Modal Voicings', 'gradient' => 'from-violet-500 to-violet-900', 'quiz' => $modalVoicings],
        ['title' => 'Scales', 'gradient' => 'from-rose-500 to-rose-900', 'quiz' => $scales],
      ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

      @foreach($courses as $course)
        @php $locked = !$course['quiz']; @endphp

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden {{ $locked ? 'opacity-60' : '' }}">

          <div class="relative h-40 bg-gradient-to-b {{ $course['gradient'] }} flex items-center justify-center">
            <span class="text-white text-xl font-bold tracking-wide text-center px-4">{{ $course['title'] }}</span>
            @if($locked)
              <div class="absolute top-3 right-3 w-7 h-7 rounded-full bg-black/30 flex items-center justify-center">
                <i class="fa-solid fa-lock text-white text-xs"></i>
              </div>
            @endif
          </div>

          <div class="p-5">
            <h3 class="text-[15px] font-bold text-gray-900 mb-4">{{ $course['title'] }}</h3>

            @if($locked)
              <span class="flex items-center justify-center gap-1 w-full text-sm font-semibold text-gray-400 border border-gray-200 rounded-full py-3 cursor-not-allowed select-none">
                COMING SOON
              </span>
            @else
              <a href="/member/ear-training/{{ $course['quiz']->id }}" class="flex items-center justify-center gap-1 w-full text-sm font-semibold text-gray-900 border border-gray-300 rounded-full py-3 hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-colors duration-200">
                START QUIZ <i class="fa-solid fa-chevron-right text-xs"></i>
              </a>
            @endif
          </div>
        </div>
      @endforeach

    </div>

  </div>
</section>

@endsection
