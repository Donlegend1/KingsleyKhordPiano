@extends('layouts.member')

@section('content')

@php
    $authUser = auth()->user();
    $initials = strtoupper(substr($authUser->first_name ?? $authUser->name ?? 'U', 0, 1) . substr($authUser->last_name ?? '', 0, 1));
    $progress = $plan->progressPercent();
    $categoryLabels = [
        'finger_exercise' => ['label' => 'Finger Exercise', 'icon' => 'fa-hand-fist'],
        'theory_and_application' => ['label' => 'Theory and Application', 'icon' => 'fa-book'],
        'guided_practice' => ['label' => 'Guided Practice', 'icon' => 'fa-bullseye'],
        'repertoire' => ['label' => 'Repertoire', 'icon' => 'fa-music'],
    ];
    $weeksByMonth = [];
    $weekGroups = [];
    foreach (array_keys($plan->months ?? []) as $index) {
        $weeksByMonth[$index] = $plan->lessonsByWeek($index);
        foreach ($weeksByMonth[$index] as $week => $lessons) {
            $weekGroups[] = ['id' => "{$index}.{$week}", 'month' => $index, 'keys' => array_column($lessons, 'lesson_key')];
        }
    }
    $progressForumUrl = route('community.forum.category', 'progress_report');
@endphp

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
    <a href="/home" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-[#C85A5A] font-medium">Personalized Plan</span>
  </div>
</section>

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6"
     x-data="personalizedPlanPage({{ \Illuminate\Support\Js::from(['progress' => $progress, 'completed' => $plan->completed_lessons ?? [], 'weeks' => $weekGroups]) }})">

  {{-- Shown right after a lesson is ticked: invites the student to post their progress --}}
  <div x-show="shareNudge" x-cloak x-transition.opacity
       class="fixed z-50 bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:bottom-6 sm:w-96 bg-white border border-[#1447A6]/20 rounded-2xl shadow-xl p-4">
    <div class="flex items-start gap-3">
      <div class="w-9 h-9 rounded-full bg-[#1447A6]/10 text-[#1447A6] flex items-center justify-center flex-shrink-0">
        <i class="fa-solid fa-comments"></i>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-gray-900">Nice work finishing <span x-text="shareNudge"></span>!</p>
        <p class="text-[13px] text-gray-600 mt-0.5">Share your progress with the community and let them cheer you on.</p>
        <a href="{{ $progressForumUrl }}"
           class="mt-3 flex w-full items-center justify-center gap-2 px-4 py-2 rounded-lg bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-bold transition-colors">
          Post my progress
          <i class="fa fa-angle-right text-xs"></i>
        </a>
      </div>
      <button type="button" @click="shareNudge = null" class="text-gray-400 hover:text-gray-600 flex-shrink-0" aria-label="Dismiss">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  </div>

  <div class="max-w-7xl mx-auto">

    {{-- Header: 3 summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

      {{-- Card 1: Student --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-[#1447A6]/10 text-[#1447A6] font-bold text-sm flex items-center justify-center flex-shrink-0">
          {{ $initials }}
        </div>
        <div class="min-w-0">
          <p class="text-[15px] font-bold text-gray-900 break-words">{{ trim(($authUser->first_name ?? '') . ' ' . ($authUser->last_name ?? '')) ?: $authUser->name }}</p>
          @if($plan->skill_level)
            <p class="text-[13px] text-gray-500">Plan Level: {{ $plan->skill_level }}</p>
          @endif
        </div>
      </div>

      {{-- Card 2: Goal --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-[#1447A6]/10 text-[#1447A6] flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-bullseye text-lg"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-gray-400">Goal</p>
          <p class="text-[15px] font-bold text-gray-900 break-words">{{ $plan->goal ?: 'Not set yet' }}</p>
        </div>
      </div>

      {{-- Card 3: Overall Progress --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-full bg-[#1447A6]/10 text-[#1447A6] flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-chart-pie text-lg"></i>
          </div>
          <div class="flex-1 flex items-center justify-between min-w-0">
            <p class="text-[15px] font-semibold text-gray-900">Overall Progress</p>
            <p class="text-[15px] font-extrabold text-gray-900" x-text="progress + '%'"></p>
          </div>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
          <div class="bg-[#1447A6] h-2.5 rounded-full transition-all duration-500" :style="'width: ' + progress + '%'"></div>
        </div>
      </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- Left: Months / Lessons --}}
      <div class="lg:col-span-2">

        <div class="flex sm:grid sm:grid-cols-3 gap-3 mb-5 overflow-x-auto sm:overflow-visible -mx-4 px-4 sm:mx-0 sm:px-0" style="scrollbar-width: none;">
          @foreach ($plan->months ?? [] as $index => $month)
            @php $tabDateRange = $plan->monthDateRange($index); @endphp
            <button type="button" @click="activeMonth = {{ $index }}"
                :class="activeMonth === {{ $index }} ? 'bg-[#1447A6] border-[#1447A6]' : 'bg-white border-gray-200 hover:bg-gray-50'"
                class="text-left px-4 py-3 rounded-xl border transition-colors flex-shrink-0 w-40 sm:w-auto">
              <p class="text-sm font-bold whitespace-nowrap" :class="activeMonth === {{ $index }} ? 'text-white' : 'text-gray-900'">Month {{ $index + 1 }}</p>
              <p class="text-xs mt-0.5 whitespace-nowrap" :class="activeMonth === {{ $index }} ? 'text-white/70' : 'text-gray-400'">
                @if($tabDateRange)
                  {{ $tabDateRange[0]->format('M j') }} - {{ $tabDateRange[1]->format('M j') }} &middot;
                @endif
                <span x-text="monthProgress({{ $index }}) + '%'">{{ $plan->monthProgressPercent($index) }}%</span>
              </p>
            </button>
          @endforeach
        </div>

        @foreach ($plan->months ?? [] as $index => $month)
          <div x-show="activeMonth === {{ $index }}" x-cloak>

            <div class="rounded-xl border border-gray-200 bg-white p-4">
              <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-sm font-bold text-gray-900">Lessons</h2>
                <div class="flex items-center gap-1 rounded-lg bg-gray-100 p-1" role="group" aria-label="Group lessons">
                  <button type="button" @click="viewMode = 'week'" :aria-pressed="viewMode === 'week'"
                          :class="viewMode === 'week' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                          class="rounded-md px-3 py-1.5 text-xs font-semibold">By Week</button>
                  <button type="button" @click="viewMode = 'category'" :aria-pressed="viewMode === 'category'"
                          :class="viewMode === 'category' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                          class="rounded-md px-3 py-1.5 text-xs font-semibold">By Category</button>
                </div>
              </div>

              <p x-show="saveError" x-cloak role="alert" class="mb-4 text-sm text-red-700">
                Progress could not be saved. Please try again.
              </p>

              <div x-show="viewMode === 'week'" class="space-y-3">
                @forelse ($weeksByMonth[$index] as $weekNumber => $lessons)
                  @php
                    $weekId = "{$index}.{$weekNumber}";
                    $weekPanel = "plan-week-{$index}-{$weekNumber}";
                  @endphp
                  <div class="rounded-xl border"
                       :class="nextWeek?.id === '{{ $weekId }}' ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-white'">
                    <h3>
                      <button type="button" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left focus-visible:ring-2 focus-visible:ring-[#1447A6]"
                              @click="toggleWeek('{{ $weekId }}')" :aria-expanded="isWeekOpen('{{ $weekId }}')" aria-controls="{{ $weekPanel }}">
                        <span class="text-sm font-bold text-gray-900">{{ $weekNumber === 'unscheduled' ? 'No Week Set' : 'Week ' . $weekNumber }}</span>
                        <span x-cloak x-show="nextWeek?.id === '{{ $weekId }}'" class="rounded-full bg-white px-2 py-0.5 text-xs font-semibold text-[#1447A6]">Next up</span>
                        <span class="ml-auto text-right text-xs text-gray-600"><span x-text="weekCompleted('{{ $weekId }}')"></span> of {{ count($lessons) }} complete</span>
                        <i class="fa-solid fa-chevron-down shrink-0 text-xs text-gray-500 transition-transform" :class="{ 'rotate-180': isWeekOpen('{{ $weekId }}') }" aria-hidden="true"></i>
                      </button>
                    </h3>
                    <div id="{{ $weekPanel }}" x-show="isWeekOpen('{{ $weekId }}')" x-cloak class="space-y-2 px-3 pb-3">
                      @foreach ($lessons as $lesson)
                        @include('memberpages.partials.personalized-plan-lesson', [
                            'lessonKey' => $lesson['lesson_key'],
                            'lessonContext' => $categoryLabels[$lesson['category_key']]['label'] ?? null,
                        ])
                      @endforeach
                    </div>
                  </div>
                @empty
                  <p class="text-sm text-gray-600">No lessons yet.</p>
                @endforelse
              </div>

              <div x-show="viewMode === 'category'" x-cloak>
                @if(empty($weeksByMonth[$index]))
                  <p class="text-sm text-gray-600">No lessons yet.</p>
                @endif
                @foreach ($categoryLabels as $key => $meta)
                  @php $lessons = $month['lessons'][$key] ?? []; @endphp
                  @if(!empty($lessons))
                    <section class="mb-5 last:mb-0">
                      <h3 class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-gray-900">
                        <i class="fa-solid {{ $meta['icon'] }} text-blue-500" aria-hidden="true"></i>
                        {{ $meta['label'] }}
                      </h3>
                      <div class="space-y-2">
                        @foreach ($lessons as $lesson)
                          @if(!empty($lesson['id']))
                            @include('memberpages.partials.personalized-plan-lesson', [
                                'lessonKey' => "{$index}.{$key}.{$lesson['id']}",
                                'lessonContext' => !empty($lesson['week']) ? 'Week ' . $lesson['week'] : null,
                            ])
                          @endif
                        @endforeach
                      </div>
                    </section>
                  @endif
                @endforeach
              </div>
            </div>

          </div>
        @endforeach

      </div>

      {{-- Right: 90-Day Target + widgets --}}
      <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <p class="text-sm font-bold text-gray-900 mb-3">90-Day Target</p>
          @if(!empty($plan->ninety_day_target))
            <ul class="space-y-2">
              @foreach ($plan->ninety_day_target as $item)
                <li class="flex items-start gap-2 text-sm text-gray-600 break-words">
                  <i class="fa-solid fa-check text-green-500 mt-0.5"></i>
                  {{ $item }}
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-sm text-gray-400 italic">No targets set yet.</p>
          @endif
        </div>

        {{-- Upcoming Live Session --}}
        @if($liveshow)
          @php
            $sessionUrl = route('member.live-session.confirm', $liveshow);
          @endphp
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5"
               data-live-session-card data-start-time="{{ $liveshow->start_time->toIso8601String() }}">
            <div class="flex items-center gap-1.5 text-green-600 text-[11px] font-bold uppercase tracking-wide mb-2">
              <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
              Upcoming Live Session
            </div>
            <p class="text-sm font-bold text-gray-900 mb-1 break-words">{{ $liveshow->title }}</p>
            <p class="text-xs text-gray-400 mb-4 break-words live-session-datetime">{{ $liveshow->start_time->format('l, M j \a\t g:i A') }} &middot; Zoom</p>

            <div class="grid grid-cols-4 gap-1.5 mb-4">
              <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg py-2 border border-gray-100">
                <span class="text-sm font-bold text-gray-900 tabular-nums live-session-days">0</span>
                <span class="text-[9px] font-semibold text-gray-400 uppercase">Days</span>
              </div>
              <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg py-2 border border-gray-100">
                <span class="text-sm font-bold text-gray-900 tabular-nums live-session-hours">0</span>
                <span class="text-[9px] font-semibold text-gray-400 uppercase">Hours</span>
              </div>
              <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg py-2 border border-gray-100">
                <span class="text-sm font-bold text-gray-900 tabular-nums live-session-minutes">0</span>
                <span class="text-[9px] font-semibold text-gray-400 uppercase">Mins</span>
              </div>
              <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg py-2 border border-gray-100">
                <span class="text-sm font-bold text-gray-900 tabular-nums live-session-seconds">0</span>
                <span class="text-[9px] font-semibold text-gray-400 uppercase">Sec</span>
              </div>
            </div>

            <a href="{{ $sessionUrl }}"
               class="flex items-center justify-center gap-1.5 w-full py-2.5 rounded-lg bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-bold transition-colors">
              Register for Session
              <i class="fa fa-angle-right text-xs"></i>
            </a>
          </div>

          <script>
            (function () {
              const card = document.querySelector('[data-live-session-card]');
              if (!card) return;
              const startDate = new Date(card.dataset.startTime);
              const startTime = startDate.getTime();

              const dateLabel = new Intl.DateTimeFormat('en-US', {
                  weekday: 'long', month: 'short', day: 'numeric',
                  hour: 'numeric', minute: '2-digit',
              }).format(startDate);
              const tzLabel = new Intl.DateTimeFormat('en-US', { timeZoneName: 'short' })
                  .formatToParts(startDate)
                  .find((p) => p.type === 'timeZoneName')?.value || '';
              card.querySelector('.live-session-datetime').textContent = `${dateLabel} ${tzLabel} · Zoom`;

              const tick = () => {
                const diff = startTime - Date.now();
                const clamp = (n) => Math.max(n, 0);
                const days = clamp(Math.floor(diff / 86400000));
                const hours = clamp(Math.floor((diff % 86400000) / 3600000));
                const minutes = clamp(Math.floor((diff % 3600000) / 60000));
                const seconds = clamp(Math.floor((diff % 60000) / 1000));

                card.querySelector('.live-session-days').textContent = days;
                card.querySelector('.live-session-hours').textContent = hours;
                card.querySelector('.live-session-minutes').textContent = minutes;
                card.querySelector('.live-session-seconds').textContent = seconds;
              };

              tick();
              setInterval(tick, 1000);
            })();
          </script>
        @else
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <div class="flex items-center justify-center gap-1.5 text-gray-400 text-[11px] font-bold uppercase tracking-wide mb-3">
              <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
              Upcoming Live Session
            </div>
            <i class="fa-regular fa-calendar text-gray-300 text-2xl mb-2"></i>
            <p class="text-sm font-semibold text-gray-500">Coming Soon</p>
            <p class="text-xs text-gray-400 mt-1">No live session scheduled yet — check back soon.</p>
          </div>
        @endif

        {{-- Weekly Activity --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <p class="text-sm font-bold text-gray-900">Weekly Activity</p>
          <p class="text-xs text-gray-400 mb-4">This week's login activity</p>

          <div class="flex justify-between items-center gap-1 mb-4">
            @foreach(['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $i => $day)
              <div class="flex flex-col items-center gap-1">
                <span class="text-[10px] font-semibold text-gray-900">{{ $day }}</span>
                @if($dayStatuses[$i] === 'neutral')
                  <div class="w-7 h-7 rounded-full flex items-center justify-center bg-gray-100">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                    </svg>
                  </div>
                @elseif($dayStatuses[$i] === 'active')
                  <div class="w-7 h-7 rounded-full flex items-center justify-center bg-green-100">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                @else
                  <div class="w-7 h-7 rounded-full flex items-center justify-center bg-red-50">
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </div>
                @endif
              </div>
            @endforeach
          </div>

          @if($streak > 0)
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-blue-900">
              <span class="font-bold">{{ $streak }}-day Streak!</span> Keep it going — don't break the chain
            </div>
          @else
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 text-xs text-gray-500">
              Log in today to start your streak!
            </div>
          @endif
        </div>

        {{-- Your Stats --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <p class="text-sm font-bold text-gray-900 mb-3">Your Stats</p>
          <div class="space-y-2.5">
            <div class="flex items-start justify-between gap-3 text-sm">
              <span class="text-gray-500">Lessons Completed</span>
              <span class="font-bold text-gray-900 text-right"><span x-text="completedLessonCount">{{ $stats['lessons_completed'] }}</span>/{{ $stats['lessons_total'] }}</span>
            </div>
            <div class="flex items-start justify-between gap-3 text-sm">
              <span class="text-gray-500">Login Streak</span>
              <span class="font-bold text-gray-900 text-right">{{ $stats['current_streak'] }} days</span>
            </div>
            <div class="flex items-start justify-between gap-3 text-sm pt-2.5 border-t border-gray-100">
              <span class="text-gray-500">Milestone Achieved</span>
              <span class="font-bold text-gray-900 text-right break-words">{{ $stats['milestone_name'] }} ({{ $stats['milestone_count'] }})</span>
            </div>
            <div class="flex items-start justify-between gap-3 text-sm">
              <span class="text-gray-500 shrink-0">Assessment Level</span>
              <span class="font-bold text-violet-600 bg-violet-50 px-2.5 py-1 rounded-md text-[10px] text-right break-words">{{ $stats['current_level'] === 'Nil' ? 'Not assessed' : $stats['current_level'] }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>

<script>
    function personalizedPlanPage(initial) {
        return {
            progress: initial.progress,
            completed: initial.completed || [],
            weeks: initial.weeks,
            activeMonth: 0,
            viewMode: 'week',
            expandedWeeks: {},
            saving: false,
            saveError: false,
            shareNudge: null,
            shareNudgeShown: false,
            shareNudgeTimer: null,

            init() {
                this.activeMonth = this.nextWeek?.month ?? this.weeks[0]?.month ?? 0;
                if (!this.nextWeek && this.weeks[0]) this.expandedWeeks[this.weeks[0].id] = true;
            },

            get nextWeek() {
                return this.weeks.find((week) => week.keys.some((key) => !this.isCompleted(key)));
            },

            get completedLessonCount() {
                return this.weeks.reduce((count, week) => count + this.completedCount(week.keys), 0);
            },

            completedCount(keys) {
                return keys.filter((key) => this.isCompleted(key)).length;
            },

            weekCompleted(id) {
                return this.completedCount(this.weeks.find((week) => week.id === id).keys);
            },

            monthProgress(month) {
                const keys = this.weeks.filter((week) => week.month === month).flatMap((week) => week.keys);
                return keys.length ? Math.round(this.completedCount(keys) / keys.length * 100) : 0;
            },

            isWeekOpen(id) {
                return this.expandedWeeks[id] ?? (this.nextWeek?.id === id);
            },

            toggleWeek(id) {
                this.expandedWeeks[id] = !this.isWeekOpen(id);
            },

            // Invite the student to post their progress, once per visit to
            // this page (reloading the page, or coming back later, resets it).
            maybeShowShareNudge(lessonName) {
                if (this.shareNudgeShown) return;
                this.shareNudgeShown = true;
                this.shareNudge = lessonName;
                clearTimeout(this.shareNudgeTimer);
                this.shareNudgeTimer = setTimeout(() => { this.shareNudge = null; }, 12000);
            },

            isCompleted(key) {
                return this.completed.includes(key);
            },

            async toggleLesson(key, input, lessonName) {
                const checked = input.checked;
                input.checked = this.isCompleted(key);
                if (this.saving) return;
                const previousWeek = this.nextWeek;
                this.saving = true;
                this.saveError = false;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch('{{ route('member.personalized-plan.lesson.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ lesson_key: key, completed: checked }),
                    });
                    if (!response.ok) throw new Error('Request failed');
                    const data = await response.json();
                    if (typeof data.progress === 'number') {
                        this.progress = data.progress;
                    }
                    this.completed = data.completed;
                    if (checked) {
                        const upcomingWeek = this.nextWeek;
                        if (previousWeek?.keys.includes(key) && this.activeMonth === previousWeek.month && upcomingWeek) {
                            this.activeMonth = upcomingWeek.month;
                        }
                        this.maybeShowShareNudge(lessonName);
                    }
                } catch (error) {
                    this.saveError = true;
                } finally {
                    this.saving = false;
                }
            },
        };
    }
</script>

@endsection
