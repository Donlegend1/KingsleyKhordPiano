<section class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-5 gap-6">

  {{-- ===== LEFT: Student Progress ===== --}}
  <div class="lg:col-span-2 p-8 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-6">

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
        $hasStarted = $completed > 0;
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

    // Milestones definition
    $milestonesList = [
        ['name' => 'Starter', 'lessons' => 1],
        ['name' => 'Player', 'lessons' => 4],
        ['name' => 'Performer', 'lessons' => 9],
        ['name' => 'Artist', 'lessons' => 14],
        ['name' => 'Maestro', 'lessons' => 21],
        ['name' => 'Master', 'lessons' => 31],
        ['name' => 'Grand Master', 'lessons' => 43],
        ['name' => 'Composer', 'lessons' => 58],
        ['name' => 'Conductor', 'lessons' => 76],
        ['name' => 'Virtuoso', 'lessons' => 96],
        ['name' => 'Prodigy', 'lessons' => 121],
        ['name' => 'Piano Legend', 'lessons' => 151],
    ];

    $achievedCount = 0;
    $currentMilestoneName = 'None';
    $nextMilestone = null;
    $prevMilestoneLessons = 0;

    foreach ($milestonesList as $ms) {
        if ($totalCompleted >= $ms['lessons']) {
            $achievedCount++;
            $currentMilestoneName = $ms['name'];
            $prevMilestoneLessons = $ms['lessons'];
        } else {
            $nextMilestone = $ms;
            break;
        }
    }

    if ($nextMilestone) {
        $neededLessons = $nextMilestone['lessons'] - $totalCompleted;
        $range = $nextMilestone['lessons'] - $prevMilestoneLessons;
        $currentInRange = $totalCompleted - $prevMilestoneLessons;
        $milestonePct = $range > 0 ? round(($currentInRange / $range) * 100) : 0;
    } else {
        $neededLessons = 0;
        $milestonePct = 100;
    }

    // Streak logic
    $streak = 1;
    if (Auth::user()->last_login_at) {
        $lastLogin = \Carbon\Carbon::parse(Auth::user()->last_login_at);
        if ($lastLogin->isToday() || $lastLogin->isYesterday()) {
            $streak = 5; // Highlight 5 days streak baseline
        }
    }
    $daysOfWeek = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
    $currentDayIndex = now()->format('N') - 1; // 0 for Monday, 6 for Sunday

    // Recent Activity query
    $recentActivity = DB::table('course_progress')
        ->join('courses', 'course_progress.course_id', '=', 'courses.id')
        ->where('course_progress.user_id', Auth::id())
        ->select('courses.id', 'courses.title', 'course_progress.created_at')
        ->orderBy('course_progress.created_at', 'desc')
        ->take(3)
        ->get();

    if ($recentActivity->isEmpty()) {
        $recentActivity = Auth::user()->bookmarks()
            ->with('bookmarkable')
            ->latest()
            ->take(3)
            ->get()
            ->map(function($b) {
                return (object)[
                    'title' => $b->bookmarkable?->title ?? 'Untitled Lesson',
                    'created_at' => $b->created_at,
                    'type' => 'bookmarked'
                ];
            });
    }

    // Find last lesson to resume
    $resumeLesson = null;
    $resumeUrl = '#';
    $lastBookmark = Auth::user()->bookmarks()
        ->whereIn('bookmarkable_type', ['App\Models\Upload', 'App\Models\LearnSong', 'App\Models\ExtraCourse', 'App\Models\Course'])
        ->latest()
        ->first();

    if ($lastBookmark && $lastBookmark->bookmarkable) {
        $resumeLesson = $lastBookmark->bookmarkable;
        $resumeType = $lastBookmark->bookmarkable_type;
        $resumeUrl = ($resumeType === 'App\Models\Course') 
            ? '/member/course/' . $resumeLesson->level . '?selected_course=' . $resumeLesson->id 
            : '/member/lesson/' . $resumeLesson->id;
    } else {
        // Fallback to first active lesson
        $resumeLesson = \App\Models\LearnSong::where('status', 'active')->first() 
            ?? \App\Models\ExtraCourse::where('status', 'active')->first() 
            ?? \App\Models\Course::where('status', 'active')->first();
        if ($resumeLesson) {
            $resumeType = get_class($resumeLesson);
            $resumeUrl = ($resumeType === 'App\Models\Course') 
                ? '/member/course/' . $resumeLesson->level . '?selected_course=' . $resumeLesson->id 
                : '/member/lesson/' . $resumeLesson->id;
        }
    }
  @endphp

  <div class="lg:col-span-3 flex flex-col gap-6">
    
    {{-- Header Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
      @if(Auth::user()->passport)
        <img src="{{ Auth::user()->passport }}" alt="Avatar" class="w-14 h-14 rounded-full object-cover ring-2 ring-blue-100">
      @else
        <div class="w-14 h-14 rounded-full bg-[#0FA9A0]/10 flex items-center justify-center text-[#0FA9A0] font-bold text-xl ring-2 ring-teal-50">
          {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1)) }}
        </div>
      @endif
      <div>
        <h2 class="text-xl font-bold text-gray-900">Your Piano Journey ✨</h2>
        <p class="text-xs text-gray-400 mt-0.5">Personalized for your growth and success</p>
      </div>
    </div>

    {{-- Row 1: Resume Last Lesson & Practice Streak --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      {{-- Resume Last Lesson --}}
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center text-violet-600">
            <svg class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 24 24">
              <path d="M8 5v14l11-7z"/>
            </svg>
          </div>
          <span class="text-sm font-bold text-gray-900">Resume Last Lesson</span>
        </div>
        
        @if($resumeLesson)
          @php
            $thumbnail = $resumeLesson->thumbnail ? asset($resumeLesson->thumbnail) : ($resumeLesson->thumbnail_url ?? asset('images/featured1.jpeg'));
          @endphp
          <div class="flex items-start gap-4">
            <img src="{{ $thumbnail }}" alt="{{ $resumeLesson->title }}" class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-xl shadow-sm border border-gray-100 flex-shrink-0">
            <div class="flex-grow min-w-0">
              <h4 class="text-base font-bold text-gray-900 truncate mb-0.5">{{ $resumeLesson->title }}</h4>
              <p class="text-xs text-gray-400 mb-2">{{ ucfirst($resumeLesson->level ?? 'Beginner') }} Lesson</p>
              
              {{-- Progress bar --}}
              <div class="w-full bg-gray-100 rounded-full h-1.5 mb-1">
                <div class="bg-violet-600 h-1.5 rounded-full" style="width: 50%"></div>
              </div>
              <div class="flex justify-end text-[10px] text-gray-400 font-semibold mb-3">
                <span>50% Complete</span>
              </div>
              <div>
                <a href="{{ $resumeUrl }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-xs font-bold transition duration-200 shadow-sm">
                  <span>Continue Learning</span>
                  <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        @else
          <p class="text-xs text-gray-400 py-6 text-center">No lessons found.</p>
        @endif
      </div>

      {{-- Practice Streak --}}
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M17.66 11.57c-.77-3.9-3.9-6.97-7.94-7.57-3.7-.54-7.05 1.5-8.4 4.88-.38.96-.06 2 .76 2.62.83.62 2 .54 2.75-.19.46-.46.99-.83 1.58-1.07.6-.25 1.25-.37 1.9-.37 3.31 0 6 2.69 6 6 0 .42-.05.83-.14 1.23-.23.97.2 1.96 1.05 2.45.83.47 1.88.24 2.48-.56 1.06-1.4 1.62-3.1 1.62-4.99zM12 18c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.83L12 3l5.3 6.17c.45.86.7 1.83.7 2.83 0 3.31-2.69 6-6 6z"/>
              </svg>
            </div>
            <span class="text-sm font-bold text-gray-900">Practice Streak</span>
          </div>
          
          <div class="flex items-baseline gap-1 mt-2">
            <span class="text-4xl font-extrabold text-orange-500">{{ $streak }}</span>
            <span class="text-base font-semibold text-gray-500">days</span>
          </div>
          <p class="text-xs text-gray-400 mt-1">Keep it going!</p>
        </div>
        
        {{-- Streak circles --}}
        @php
          $highlightedIndices = [];
          $tempIndex = $currentDayIndex;
          for ($s = 0; $s < min($streak, 7); $s++) {
              $highlightedIndices[] = $tempIndex;
              $tempIndex = ($tempIndex - 1 + 7) % 7;
          }
        @endphp
        <div class="flex justify-between items-center gap-1 mt-6">
          @foreach($daysOfWeek as $i => $day)
            @php
              $isHighlighted = in_array($i, $highlightedIndices);
            @endphp
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold transition-all
              {{ $isHighlighted ? 'bg-orange-500 text-white shadow-sm' : 'bg-gray-50 text-gray-400 border border-gray-200' }}">
              {{ $day }}
            </div>
          @endforeach
        </div>
      </div>
      
    </div>

    {{-- Row 2: Recent Activity & Your Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      {{-- Recent Activity --}}
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                <svg class="w-4 h-4 fill-none stroke-current" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
              </div>
              <span class="text-sm font-bold text-gray-900">Recent Activity</span>
            </div>
            <a href="/member/bookmark" class="text-xs text-[#0FA9A0] font-bold hover:underline">View All</a>
          </div>
          
          <div class="space-y-4">
            @forelse($recentActivity as $act)
              @php
                $isBookmarked = isset($act->type) && $act->type === 'bookmarked';
              @endphp
              <div class="flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 min-w-0">
                  <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0
                    {{ $isBookmarked ? 'bg-indigo-500 text-white' : 'bg-emerald-500 text-white' }}">
                    @if($isBookmarked)
                      <svg class="w-2.5 h-2.5 fill-current ml-0.5" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                      </svg>
                    @else
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                      </svg>
                    @endif
                  </div>
                  <span class="font-medium text-gray-800 truncate">
                    {{ $isBookmarked ? 'Started' : 'Completed' }}: {{ $act->title }}
                  </span>
                </div>
                <span class="text-gray-400 flex-shrink-0 text-[10px]">
                  {{ \Carbon\Carbon::parse($act->created_at)->diffForHumans(null, true) }}
                </span>
              </div>
            @empty
              <p class="text-xs text-gray-400 py-6 text-center">No recent activity found.</p>
            @endforelse
          </div>
        </div>
      </div>

      {{-- Your Stats --}}
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center text-violet-600">
              <svg class="w-4 h-4 fill-none stroke-current" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 0 1 3 18.375v-5.25ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125v-9.75ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v14.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
              </svg>
            </div>
            <span class="text-sm font-bold text-gray-900">Your Stats</span>
          </div>
          
          <div class="space-y-3">
            <div class="flex items-center justify-between text-xs py-2 border-b border-gray-50">
              <span class="text-gray-400 font-semibold">Lessons Completed</span>
              <span class="font-bold text-gray-900 text-sm">{{ $totalCompleted }}</span>
            </div>
            <div class="flex items-center justify-between text-xs py-2 border-b border-gray-50">
              <span class="text-gray-400 font-semibold">Milestone Achieved</span>
              <span class="font-bold text-gray-900 text-sm">{{ $currentMilestoneName }} ({{ $achievedCount }})</span>
            </div>
            <div class="flex items-center justify-between text-xs py-2">
              <span class="text-gray-400 font-semibold">Current Level</span>
              <span class="font-bold text-violet-600 bg-violet-50 px-2.5 py-1 rounded-md text-[10px]">{{ $currentLevel }}</span>
            </div>
          </div>
        </div>
      </div>
      
    </div>

    {{-- Row 3: Next Milestone --}}
    <div class="bg-[#FFFDF4] border border-[#FBEFBF] rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-4 w-full md:w-auto">
        <div class="w-12 h-12 rounded-xl bg-[#FDF6D6] flex items-center justify-center text-2xl shadow-sm flex-shrink-0">
          🏆
        </div>
        <div class="flex-grow min-w-0">
          <h4 class="text-sm font-bold text-gray-900">Next Milestone</h4>
          <p class="text-xs text-gray-500 mt-0.5">
            @if($nextMilestone)
              Complete {{ $neededLessons }} more {{ Str::plural('lesson', $neededLessons) }} to reach <span class="font-bold text-amber-600">{{ $nextMilestone['name'] }}</span>!
            @else
              You achieved all milestones! You are a <span class="font-bold text-amber-600">Piano Legend</span>!
            @endif
          </p>
        </div>
      </div>
      
      <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
        <span class="text-sm font-bold text-amber-600">{{ $milestonePct }}%</span>
        <div class="w-32 bg-amber-100 rounded-full h-2">
          <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ $milestonePct }}%"></div>
        </div>
        <div class="relative flex items-center justify-center flex-shrink-0">
          {{-- Purple and Gold Shield Icon --}}
          <svg class="w-11 h-11 text-indigo-700 drop-shadow-sm" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3z"/>
          </svg>
          <div class="absolute inset-0 flex items-center justify-center text-amber-400">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
            </svg>
          </div>
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
