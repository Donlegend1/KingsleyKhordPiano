@extends('layouts.member')

@section('content')

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
    <a href="/home" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <a href="/member/roadmap" class="text-blue-600 font-medium">Roadmap</a>
  </div>
</section>

<div class="max-w-6xl mx-auto px-6 py-10">

  {{-- Title --}}
  <div class="mb-8">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-1">Your Learning Roadmap</h1>
    <div class="w-10 h-1 bg-[#6366F1] rounded mb-3"></div>
    <p class="text-gray-500 text-sm">Choose your path and start building the skills that will take your musicianship to the next level.</p>
  </div>

  {{-- Not Sure Banner --}}
  <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-8 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">
    <div class="flex items-start gap-3">
      <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
        </svg>
      </div>
      <div>
        <p class="text-sm font-semibold text-gray-800">Not sure which level to choose?</p>
        <p class="text-xs text-gray-500 mt-0.5">You can take our quick assessment to find the best level for you.</p>
      </div>
    </div>
    <a href="/member/quiz" class="inline-flex items-center flex-shrink-0 text-indigo-600 text-sm font-semibold hover:underline">
      Take Assessment
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>
    </a>
  </div>

  {{-- Cards --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Card 1: Beginner --}}
    @php $b = $progress['Beginner']; @endphp
    <div class="bg-white rounded-2xl border-2 {{ $b['completed'] > 0 ? 'border-[#6366F1]' : 'border-gray-100' }} shadow-sm flex flex-col overflow-hidden">
      <div class="p-4 flex flex-col flex-1">
        <div class="rounded-xl overflow-hidden bg-gray-100 mb-4">
          <img src="/images/featured1.jpeg" alt="Beginner" class="w-full h-40 object-cover">
        </div>
        <h2 class="text-base font-bold text-gray-900 mb-1">Beginner Course</h2>
        <p class="text-gray-500 text-sm mb-4">Start your piano journey with a clear, step-by-step method designed to make learning both easy and practical.</p>
        <div class="mt-auto">
          <div class="mb-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
              <span>{{ $b['completed'] }}/{{ $b['total'] }} courses completed</span>
              <span>{{ $b['pct'] }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
              <div class="bg-[#6366F1] h-2 rounded-full transition-all duration-500" style="width: {{ $b['pct'] }}%"></div>
            </div>
          </div>
          <a href="/member/course/beginner"
             class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#6366F1] hover:bg-[#4F46E5] text-white font-semibold rounded-xl transition">
            {{ $b['completed'] > 0 ? 'Continue Course' : 'Start Course' }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
      </div>
    </div>

    {{-- Card 2: Intermediate --}}
    @php $i = $progress['Intermediate']; @endphp
    <div class="bg-white rounded-2xl border-2 {{ $i['completed'] > 0 ? 'border-[#22C55E]' : 'border-gray-100' }} shadow-sm flex flex-col overflow-hidden">
      <div class="p-4 flex flex-col flex-1">
        <div class="rounded-xl overflow-hidden bg-gray-100 mb-4">
          <img src="/images/featured2.jpeg" alt="Intermediate" class="w-full h-40 object-cover">
        </div>
        <h2 class="text-base font-bold text-gray-900 mb-1">Intermediate Course</h2>
        <p class="text-gray-500 text-sm mb-4">Once you've mastered the basics, take your skills to the next level with this intermediate course.</p>
        <div class="mt-auto">
          <div class="mb-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
              <span>{{ $i['completed'] }}/{{ $i['total'] }} courses completed</span>
              <span>{{ $i['pct'] }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
              <div class="bg-[#22C55E] h-2 rounded-full transition-all duration-500" style="width: {{ $i['pct'] }}%"></div>
            </div>
          </div>
          <a href="/member/course/intermediate"
             class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#22C55E] hover:bg-[#16A34A] text-white font-semibold rounded-xl transition">
            {{ $i['completed'] > 0 ? 'Continue Course' : 'Start Course' }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
      </div>
    </div>

    {{-- Card 3: Advanced --}}
    @php $a = $progress['Advanced']; @endphp
    <div class="bg-white rounded-2xl border-2 {{ $a['completed'] > 0 ? 'border-[#EF4444]' : 'border-gray-100' }} shadow-sm flex flex-col overflow-hidden">
      <div class="p-4 flex flex-col flex-1">
        <div class="rounded-xl overflow-hidden bg-gray-100 mb-4">
          <img src="/images/featured3.jpeg" alt="Advanced" class="w-full h-40 object-cover">
        </div>
        <h2 class="text-base font-bold text-gray-900 mb-1">Advanced Course</h2>
        <p class="text-gray-500 text-sm mb-4">After refining your intermediate skills, take the leap into advanced playing with this course.</p>
        <div class="mt-auto">
          <div class="mb-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
              <span>{{ $a['completed'] }}/{{ $a['total'] }} courses completed</span>
              <span>{{ $a['pct'] }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
              <div class="bg-[#EF4444] h-2 rounded-full transition-all duration-500" style="width: {{ $a['pct'] }}%"></div>
            </div>
          </div>
          <a href="/member/course/advanced"
             class="flex items-center justify-center gap-2 w-full py-3.5 bg-[#EF4444] hover:bg-[#DC2626] text-white font-semibold rounded-xl transition">
            {{ $a['completed'] > 0 ? 'Continue Course' : 'Start Course' }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
