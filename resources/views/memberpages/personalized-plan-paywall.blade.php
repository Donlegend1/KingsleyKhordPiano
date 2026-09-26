@extends('layouts.member')

@section('content')

@php
  $upgradeRequestUrl = '/member/support?' . http_build_query([
      'subject' => 'Request to Upgrade to Premium Plan',
      'message' => "Hi, I'm currently on the Standard plan. Please upgrade my account to the Premium plan.",
  ]);
@endphp

<div class="min-h-screen bg-gray-50 px-4 pt-16 pb-16">
  <div class="max-w-6xl mx-auto">

    <div class="text-center mb-10">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">You&rsquo;re Not on the Premium Side Yet</h1>
      <p class="text-base text-gray-500">Take your piano journey further with Premium</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      {{-- Standard Members --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-crosshairs text-indigo-400"></i>
          </div>
          <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wide font-sf">Standard Member</h2>
        </div>
        <hr class="border-gray-100 mb-2">
        <ul class="divide-y divide-gray-50">
          @foreach ([
              ['ok' => true, 'text' => 'Access to learning roadmap'],
              ['ok' => true, 'text' => 'Guided practice, courses, ear training quiz'],
              ['ok' => false, 'text' => 'No personalized practice plan'],
              ['ok' => false, 'text' => 'No accountability framework'],
              ['ok' => false, 'text' => 'No direct feedback from Kingsley'],
              ['ok' => false, 'text' => 'No structured direction'],
              ['ok' => false, 'text' => 'No in-depth master classes'],
          ] as $item)
            <li class="flex items-center gap-3 py-3.5">
              <span class="w-6 h-6 flex items-center justify-center rounded-full flex-shrink-0 {{ $item['ok'] ? 'bg-emerald-50' : 'bg-red-50' }}">
                <i class="fa-solid {{ $item['ok'] ? 'fa-check text-emerald-500' : 'fa-xmark text-red-400' }} text-[11px]"></i>
              </span>
              <span class="text-sm {{ $item['ok'] ? 'text-gray-700' : 'text-gray-400' }}">{{ $item['text'] }}</span>
            </li>
          @endforeach
        </ul>
      </div>

      {{-- Premium Members --}}
      <div class="relative bg-gradient-to-b from-[#1447A6]/[0.04] to-white rounded-2xl border-2 border-[#1447A6]/20 shadow-md shadow-[#1447A6]/5 p-8 flex flex-col">
        <span class="absolute -top-3 right-8 inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#1447A6] text-white text-[11px] font-bold uppercase tracking-wide">
          <i class="fa-solid fa-star text-[9px]"></i> Recommended
        </span>
        <div class="flex items-center gap-3 mb-6">
          <div class="w-10 h-10 rounded-xl bg-[#1447A6]/10 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-gem text-[#1447A6]"></i>
          </div>
          <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wide font-sf">Premium Member</h2>
        </div>
        <hr class="border-[#1447A6]/10 mb-2">
        <ul class="divide-y divide-gray-50 mb-6">
          @foreach ([
              'Access to Full Learning Roadmap',
              'Courses and Lessons tailored to your skill level',
              '90-day personalized practice plan built around your goals',
              'Accountability, progress check-ins & ongoing directions from Kingsley',
              'Individualized feedback to help you catch and correct mistakes fast',
              'Clear, Structured Direction & Step-by-Step Learning Paths',
              'In-Depth Masterclasses & Advanced Deep-Dive Sessions',
          ] as $text)
            <li class="flex items-center gap-3 py-3.5">
              <span class="w-6 h-6 flex items-center justify-center rounded-full flex-shrink-0 bg-[#1447A6]/10">
                <i class="fa-solid fa-check text-[#1447A6] text-[11px]"></i>
              </span>
              <span class="text-sm text-gray-700 font-medium">{{ $text }}</span>
            </li>
          @endforeach
        </ul>
        <a href="{{ $upgradeRequestUrl }}"
           class="mt-auto inline-flex items-center justify-center w-full py-3 rounded-lg bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-bold transition-colors">
          Upgrade to Premium Plan
        </a>
        <div class="flex items-start gap-2 text-xs text-gray-500 mt-3">
          <i class="fa-solid fa-circle-info mt-0.5 text-[#1447A6]"></i>
          <span>Any remaining balance on your current plan will be credited toward your Premium upgrade.</span>
        </div>
      </div>

    </div>

    {{-- Premium benefits --}}
    <div class="mt-16 text-center">
      <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-2">Take Your Piano Playing to the Next Level</h2>
      <p class="text-base text-gray-500 mb-8">Get the structure, guidance, and support you need to make faster progress and grow alongside a community of dedicated pianists.</p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 text-left">
        @foreach ([
            ['title' => 'Your 90-Day Piano Transformation', 'text' => 'Follow a personalized plan designed to take your playing from where you are to where you want to be.'],
            ['title' => 'Your Next Step, Every Week', 'text' => 'Know exactly what to practice, what to learn, and where to focus each week.'],
            ['title' => 'Learn Together. Grow Together.', 'text' => 'Join live piano sessions, ask questions, share your progress, and learn alongside other serious musicians.'],
            ['title' => 'Progress You Can See', 'text' => 'Track your lessons, practice, and milestones so your musical growth never feels invisible.'],
            ['title' => 'Real Feedback. Real Improvement.', 'text' => 'Share your playing and get practical feedback to help you overcome weaknesses and improve with intention.'],
            ['title' => 'Your Path to Mastery', 'text' => 'Build the right skills in the right order with a roadmap shaped around your level and musical goals.'],
        ] as $benefit)
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 text-base mb-2">{{ $benefit['title'] }}</h3>
            <p class="text-sm text-gray-500 leading-relaxed">{{ $benefit['text'] }}</p>
          </div>
        @endforeach
      </div>

      <a href="{{ $upgradeRequestUrl }}"
         class="inline-flex items-center justify-center px-10 py-3 mt-10 rounded-lg bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-bold transition-colors">
        Upgrade to Premium Plan
      </a>
      <p class="flex items-center justify-center gap-2 text-xs text-gray-500 mt-3">
        <i class="fa-solid fa-circle-info text-[#1447A6]"></i>
        Any remaining balance on your current plan will be credited toward your Premium upgrade.
      </p>
    </div>
  </div>
</div>

@endsection
