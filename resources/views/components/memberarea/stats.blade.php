<section class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 md:grid-cols-2 gap-6">

  {{-- ===== LEFT: Student Progress ===== --}}
  <div class="p-8 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
      <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-md">
        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 12h4v9H3v-9zm7-5h4v14h-4V7zm7-4h4v18h-4V3z"/>
        </svg>
      </div>
      <div>
        <h2 class="text-xl font-bold text-[#1E2A3A]">Student Progress</h2>
        <p class="text-sm text-gray-400 mt-0.5">Track your learning milestones</p>
      </div>
    </div>

    <div class="border-t border-gray-100"></div>

    {{-- Level rows --}}
    @php $levels = ['Beginner', 'Intermediate', 'Advanced']; @endphp

    @foreach ($levels as $level)
      @php
        $total     = $progress[$level]['total'] ?? 0;
        $completed = $progress[$level]['completed'] ?? 0;
        $pct       = $total > 0 ? round(($completed / $total) * 100) : 0;
        $hasStarted = !empty($completedByLevel[$level]);
        $colors = ['Beginner' => 'bg-violet-500', 'Intermediate' => 'bg-blue-500', 'Advanced' => 'bg-indigo-600'];
        $barColor = $colors[$level];
      @endphp

      <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between">
          <span class="font-semibold text-[#1E2A3A] text-sm">{{ $level }}</span>
          <span class="text-xs text-gray-400">{{ $pct }}%</span>
        </div>
        <div class="flex items-center gap-3">
          <span class="text-blue-600 font-bold text-sm">{{ $completed }}/{{ $total }}</span>
          <span class="text-gray-400 text-sm">courses completed</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2">
          <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
        </div>
        <a href="/member/course/{{ urlencode(strtolower($level)) }}"
           class="inline-flex items-center gap-2 self-start px-5 py-2 rounded-full border border-gray-300 text-[#1E2A3A] text-sm font-medium hover:border-blue-500 hover:text-blue-600 transition-colors">
          {{ $hasStarted ? 'Continue' : 'Start' }} {{ $level }}
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>

      @if (!$loop->last)
        <div class="border-t border-gray-100"></div>
      @endif
    @endforeach

  </div>


  {{-- ===== RIGHT: Your Piano Journey ===== --}}
  @php
    $totalCompleted = collect($progress)->sum('completed');
    $totalCourses   = collect($progress)->sum('total');
    $overallPct     = $totalCourses > 0 ? round(($totalCompleted / $totalCourses) * 100) : 0;
    $currentLevel   = 'Beginner';
    foreach (['Intermediate', 'Advanced'] as $lvl) {
      if (($progress[$lvl]['completed'] ?? 0) > 0) $currentLevel = $lvl;
    }
  @endphp

  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">

    {{-- Header --}}
    <div class="px-8 pt-8 pb-5 flex items-center gap-4 border-b border-gray-100">
      <img src="/images/engagement.png" alt="Avatar" class="w-14 h-14 rounded-full object-cover ring-2 ring-blue-100">
      <div>
        <h2 class="text-xl font-bold text-[#1E2A3A]">Your Piano Journey ✨</h2>
        <p class="text-sm text-gray-400 mt-0.5">Personalized for your growth and success</p>
      </div>
    </div>

    <div class="px-8 py-6 flex flex-col gap-5 flex-1">

      {{-- Stats Row --}}
      <div class="grid grid-cols-2 gap-4">

        <div class="bg-gray-50 rounded-xl p-4 flex flex-col gap-1">
          <div class="flex items-center gap-2 text-gray-400 text-xs font-medium mb-1">
            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M3 12h4v9H3v-9zm7-5h4v14h-4V7zm7-4h4v18h-4V3z"/></svg>
            Overall Progress
          </div>
          <span class="text-2xl font-bold text-[#1E2A3A]">{{ $overallPct }}<span class="text-base font-normal text-gray-400">%</span></span>
          <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
            <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $overallPct }}%"></div>
          </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-4 flex flex-col gap-1">
          <div class="flex items-center gap-2 text-gray-400 text-xs font-medium mb-1">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Lessons Done
          </div>
          <span class="text-2xl font-bold text-[#1E2A3A]">{{ $totalCompleted }}<span class="text-base font-normal text-gray-400">/{{ $totalCourses }}</span></span>
          <p class="text-xs text-gray-400 mt-0.5">Level: <span class="font-semibold text-blue-600">{{ $currentLevel }}</span></p>
        </div>

      </div>

      {{-- Next Milestone --}}
      <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <span class="text-xl">🏆</span>
            <div>
              <p class="font-semibold text-[#1E2A3A] text-sm">Next Milestone</p>
              <p class="text-xs text-gray-500">Keep going to unlock your next badge!</p>
            </div>
          </div>
          <span class="text-amber-500 font-bold text-sm">{{ $overallPct }}%</span>
        </div>
        <div class="w-full bg-amber-100 rounded-full h-2">
          <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ $overallPct }}%"></div>
        </div>
      </div>

      {{-- Engagement Cards --}}
      <div class="flex flex-col gap-3">

        <div onclick="openModal('qaModal')" class="cursor-pointer group flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition-all">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div>
              <p class="font-semibold text-[#1E2A3A] text-sm">Question & Answer</p>
              <p class="text-xs text-gray-400">Submit a question and get community answers</p>
            </div>
          </div>
          <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>

        <div onclick="openModal('requestModal')" class="cursor-pointer group flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition-all">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <div>
              <p class="font-semibold text-[#1E2A3A] text-sm">Course Request</p>
              <p class="text-xs text-gray-400">Request for your needed course here</p>
            </div>
          </div>
          <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>

        <div onclick="openModal('progressModal')" class="cursor-pointer group flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition-all">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
              <p class="font-semibold text-[#1E2A3A] text-sm">Student Progress</p>
              <p class="text-xs text-gray-400">A snapshot of learning milestones</p>
            </div>
          </div>
          <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>

      </div>

    </div>
  </div>

</section>


{{-- ===== Modals ===== --}}
@foreach ([
    ['id' => 'qaModal', 'title' => 'Question & Answer',
     'text' => 'Stuck on something or just curious about a concept? Drop your question here and get thoughtful, practical answers from fellow learners and instructors who have likely walked that same path. Join a community of real conversations, shared struggles and helpful insights.',
     'link' => '/member/community/space/exclusive-feed'],
    ['id' => 'requestModal', 'title' => 'Course Request',
     'text' => 'Have a course idea or something specific you are eager to learn? This is where you can let us know. We are always building with you in mind and your request might just be the next lesson we create.',
     'link' => '/member/community/space/exclusive-feed'],
    ['id' => 'progressModal', 'title' => 'Student Progress',
     'text' => 'Whether it is a small or a big breakthrough, share your progress here. It helps others see what is possible and gives you the chance to receive encouragement, support and honest feedback from a community that is rooting for you.',
     'link' => '/member/community/space/progress-report'],
] as $modal)
  <div id="{{ $modal['id'] }}" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-8">
      <button onclick="closeModal('{{ $modal['id'] }}')" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition text-xl font-bold">&times;</button>
      <h3 class="font-bold text-[#1E2A3A] text-xl mb-4">{{ $modal['title'] }}</h3>
      <p class="text-sm text-gray-500 leading-relaxed mb-8">{{ $modal['text'] }}</p>
      <a href="{{ $modal['link'] }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1E2A3A] text-white rounded-full text-sm font-medium hover:bg-blue-600 transition-colors">
        Click here
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
@endforeach

<script>
  function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
  function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>
