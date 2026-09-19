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
        ['title' => 'Relative Pitch', 'category' => 'Relative Pitch', 'gradient' => 'from-red-500 to-red-900', 'bar' => 'bg-red-600', 'quiz' => $relativePitch],
        ['title' => 'Melodic Dictation', 'category' => 'Melodic Dictation', 'gradient' => 'from-yellow-400 to-yellow-700', 'bar' => 'bg-yellow-500', 'quiz' => $melodicDictation],
        ['title' => 'Intervals', 'category' => 'Diatonic Intervals', 'gradient' => 'from-green-500 to-green-900', 'bar' => 'bg-green-600', 'quiz' => $intervals],
        ['title' => 'Basic Triads', 'category' => 'Basic Triad', 'gradient' => 'from-blue-500 to-blue-900', 'bar' => 'bg-blue-600', 'quiz' => $basicTriads],
        ['title' => 'Add 9 & b9', 'category' => 'Add 9 & b9', 'gradient' => 'from-teal-400 to-teal-800', 'bar' => 'bg-teal-500', 'quiz' => $add9],
        ['title' => '7th Degree Chords', 'category' => '7th Degree Chords', 'gradient' => 'from-purple-600 to-purple-900', 'bar' => 'bg-purple-600', 'quiz' => $seventhDegree],
        ['title' => 'Secondary 7th Chords', 'category' => 'Secondary 7th Chords', 'gradient' => 'from-fuchsia-600 to-fuchsia-900', 'bar' => 'bg-fuchsia-600', 'quiz' => $secondarySeventh],
        ['title' => 'Secondary 9th Chords', 'category' => 'Secondary 9th Chords', 'gradient' => 'from-lime-500 to-lime-800', 'bar' => 'bg-lime-600', 'quiz' => $secondaryNinth],
        ['title' => '9th Degree Chords', 'category' => '9th Degree Chords', 'gradient' => 'from-yellow-400 to-amber-700', 'bar' => 'bg-amber-600', 'quiz' => $ninthDegree],
        ['title' => '11th Degree Chords', 'category' => '11th Degree Chords', 'gradient' => 'from-cyan-500 to-cyan-900', 'bar' => 'bg-cyan-600', 'quiz' => $eleventhDegree],
        ['title' => 'Secondary 11th Chords', 'category' => 'Secondary 11th Chords', 'gradient' => 'from-sky-500 to-sky-900', 'bar' => 'bg-sky-600', 'quiz' => $secondaryEleventh],
        ['title' => '13th Degree Chords', 'category' => '13th Degree Chords', 'gradient' => 'from-orange-500 to-orange-900', 'bar' => 'bg-orange-600', 'quiz' => $thirteenthDegree],
        ['title' => 'Extentions recognition', 'category' => 'Extentions recognition', 'gradient' => 'from-pink-500 to-pink-900', 'bar' => 'bg-pink-600', 'quiz' => $extensionsRecognition],
        ['title' => 'Chord Progressions', 'category' => 'Chord Progressions', 'gradient' => 'from-emerald-500 to-emerald-900', 'bar' => 'bg-emerald-600', 'quiz' => $chordProgressions],
        ['title' => 'Modal Voicings', 'category' => 'Modal Voicings', 'gradient' => 'from-violet-500 to-violet-900', 'bar' => 'bg-violet-600', 'quiz' => $modalVoicings],
        ['title' => 'Scales', 'category' => 'Scales', 'gradient' => 'from-rose-500 to-rose-900', 'bar' => 'bg-rose-600', 'quiz' => $scales],
      ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

      @foreach($courses as $course)
        @php
          $locked = !$course['quiz'];
          $progress = $categoryProgress[$course['category']] ?? ['total' => 0, 'completed' => 0, 'pct' => 0];
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 {{ $locked ? 'opacity-60' : '' }}">

          <div class="relative h-32 bg-gradient-to-b {{ $course['gradient'] }} flex items-center justify-center">
            <span class="text-white text-xl font-bold tracking-wide text-center px-4">{{ $course['title'] }}</span>
            @if($locked)
              <div class="absolute top-3 right-3 w-7 h-7 rounded-full bg-black/30 flex items-center justify-center">
                <i class="fa-solid fa-lock text-white text-xs"></i>
              </div>
            @endif
          </div>

          <div class="p-5">
            @if(!$locked)
              <div class="mb-4">
                <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                  <span>{{ $progress['completed'] }}/{{ $progress['total'] }} lessons completed</span>
                  <span class="font-semibold text-gray-700">{{ $progress['pct'] }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                  <div class="{{ $course['bar'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $progress['pct'] }}%"></div>
                </div>
              </div>
            @endif

            @if($locked)
              <span class="flex items-center justify-center gap-1 w-full text-sm font-semibold text-gray-400 border border-gray-200 rounded-full py-3 cursor-not-allowed select-none">
                COMING SOON
              </span>
            @else
              <a href="/member/ear-training/{{ $course['quiz']->id }}" class="flex items-center justify-center gap-1 w-full text-sm font-semibold text-gray-900 border border-gray-300 rounded-full py-3 hover:bg-gray-900 hover:text-white hover:border-gray-900 transition-colors duration-200">
                {{ $progress['pct'] > 0 && $progress['pct'] < 100 ? 'CONTINUE QUIZ' : 'START QUIZ' }} <i class="fa-solid fa-chevron-right text-xs"></i>
              </a>
            @endif
          </div>
        </div>
      @endforeach

    </div>

  </div>
</section>

@endsection
