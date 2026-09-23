@extends('layouts.member')

@section('content')

@php
    $authUser = auth()->user();
    $initials = strtoupper(substr($authUser->first_name ?? $authUser->name ?? 'U', 0, 1) . substr($authUser->last_name ?? '', 0, 1));
    $progress = $plan->progressPercent();
@endphp

<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
  <div class="max-w-7xl mx-auto flex items-center h-8 gap-2 text-sm text-gray-500">
    <a href="/home" class="hover:text-gray-700">Dashboard</a>
    <span>/</span>
    <span class="text-[#C85A5A] font-medium">Personalized Plan</span>
  </div>
</section>

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6"
     x-data="personalizedPlanPage({{ \Illuminate\Support\Js::from(['progress' => $progress, 'completed' => $plan->completed_lessons ?? []]) }})">

  <div class="max-w-7xl mx-auto" x-data="{ activeMonth: 0 }">

    {{-- Header: 3 summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

      {{-- Card 1: Student --}}
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-[#1447A6]/10 text-[#1447A6] font-bold text-sm flex items-center justify-center flex-shrink-0">
          {{ $initials }}
        </div>
        <div class="min-w-0">
          <p class="text-[15px] font-bold text-gray-900 truncate">{{ trim(($authUser->first_name ?? '') . ' ' . ($authUser->last_name ?? '')) ?: $authUser->name }}</p>
          @if($plan->skill_level)
            <p class="text-[13px] text-gray-500">{{ $plan->skill_level }}</p>
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
          <p class="text-[15px] font-bold text-gray-900 truncate">{{ $plan->goal ?: 'Not set yet' }}</p>
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
                {{ $plan->monthProgressPercent($index) }}%
              </p>
            </button>
          @endforeach
        </div>

        @foreach ($plan->months ?? [] as $index => $month)
          <div x-show="activeMonth === {{ $index }}" x-cloak>

            @php
              $categoryLabels = [
                  'finger_exercise' => ['label' => 'Finger Exercise', 'icon' => 'fa-hand-fist'],
                  'theory_and_application' => ['label' => 'Theory and Application', 'icon' => 'fa-book'],
                  'guided_practice' => ['label' => 'Guided Practice', 'icon' => 'fa-bullseye'],
                  'repertoire' => ['label' => 'Repertoire', 'icon' => 'fa-music'],
              ];

              // Same lessons re-grouped by week (across all 4 categories) for the
              // alternate "by week" view — falls back into a "No Week Set" bucket
              // when a lesson has no week number assigned.
              $lessonsByWeek = [];
              foreach ($categoryLabels as $key => $meta) {
                  foreach ($month['lessons'][$key] ?? [] as $lesson) {
                      $weekNumber = is_numeric($lesson['week'] ?? null) ? (int) $lesson['week'] : null;
                      $lessonsByWeek[$weekNumber][] = $lesson + ['category_key' => $key, 'category_label' => $meta['label'], 'category_icon' => $meta['icon']];
                  }
              }
              ksort($lessonsByWeek);
            @endphp

            <div class="bg-white border border-gray-200 rounded-xl p-4" x-data="{ viewMode: 'category' }">
              <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-900">Lessons</p>
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                  <button type="button" @click="viewMode = 'category'"
                          :class="viewMode === 'category' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                          class="w-7 h-7 flex items-center justify-center rounded-md transition-colors"
                          title="Group by practice menu">
                    <i class="fa-solid fa-list text-xs"></i>
                  </button>
                  <button type="button" @click="viewMode = 'week'"
                          :class="viewMode === 'week' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-400 hover:text-gray-600'"
                          class="w-7 h-7 flex items-center justify-center rounded-md transition-colors"
                          title="Group by week">
                    <i class="fa-solid fa-calendar-week text-xs"></i>
                  </button>
                </div>
              </div>

              {{-- View 1: grouped by practice menu (category) --}}
              <div x-show="viewMode === 'category'">
                @foreach ($categoryLabels as $key => $meta)
                  @php $lessons = $month['lessons'][$key] ?? []; @endphp
                  @if(!empty($lessons))
                    <div class="mb-5 last:mb-0">
                      <p class="flex items-center gap-2 text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">
                        <i class="fa-solid {{ $meta['icon'] }} text-blue-500"></i>
                        {{ $meta['label'] }}
                      </p>
                      <div class="space-y-2">
                        @foreach ($lessons as $lesson)
                          @php $lessonKey = "{$index}.{$key}.{$lesson['id']}"; @endphp
                          <div class="flex items-center gap-3 px-4 py-3 rounded-lg border transition-colors"
                               :class="isCompleted('{{ $lessonKey }}') ? 'bg-green-50 border-green-300' : 'bg-gray-50 border-transparent'">
                            <a href="{{ $lesson['url'] ?: '#' }}"
                               target="{{ $lesson['url'] ? '_blank' : '_self' }}" rel="noopener noreferrer"
                               class="flex-1 min-w-0 hover:text-[#1447A6] transition-colors">
                              <p class="text-sm font-medium text-gray-800 truncate"
                                 :class="isCompleted('{{ $lessonKey }}') ? 'line-through text-gray-400' : ''">{{ $lesson['name'] ?: 'Untitled lesson' }}</p>
                              @if(!empty($lesson['week']) || !empty($lesson['duration']))
                                <p class="text-xs text-gray-400 mt-0.5">
                                  @if(!empty($lesson['week'])) Week {{ $lesson['week'] }} @endif
                                  @if(!empty($lesson['week']) && !empty($lesson['duration'])) &middot; @endif
                                  @if(!empty($lesson['duration'])) {{ $lesson['duration'] }} min @endif
                                </p>
                              @endif
                            </a>
                            <input type="checkbox"
                                   class="w-5 h-5 rounded border-2 border-gray-400 bg-white text-green-600 focus:ring-green-500 flex-shrink-0"
                                   :checked="isCompleted('{{ $lessonKey }}')"
                                   @change="toggleLesson('{{ $lessonKey }}', $event.target.checked)">
                          </div>
                        @endforeach
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>

              {{-- View 2: grouped by week (across all practice menus) --}}
              <div x-show="viewMode === 'week'" x-cloak>
                @forelse ($lessonsByWeek as $weekNumber => $lessons)
                  <div class="mb-5 last:mb-0">
                    <p class="text-xs font-bold text-gray-900 uppercase tracking-wide mb-2">
                      {{ $weekNumber !== null ? 'Week ' . $weekNumber : 'No Week Set' }}
                    </p>
                    <div class="space-y-2">
                      @foreach ($lessons as $lesson)
                        @php $lessonKey = "{$index}.{$lesson['category_key']}.{$lesson['id']}"; @endphp
                        <div class="flex items-center gap-3 px-4 py-3 rounded-lg border transition-colors"
                             :class="isCompleted('{{ $lessonKey }}') ? 'bg-green-50 border-green-300' : 'bg-gray-50 border-transparent'">
                          <a href="{{ $lesson['url'] ?: '#' }}"
                             target="{{ $lesson['url'] ? '_blank' : '_self' }}" rel="noopener noreferrer"
                             class="flex-1 min-w-0 hover:text-[#1447A6] transition-colors">
                            <p class="text-sm font-medium text-gray-800 truncate"
                               :class="isCompleted('{{ $lessonKey }}') ? 'line-through text-gray-400' : ''">{{ $lesson['name'] ?: 'Untitled lesson' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                              <i class="fa-solid {{ $lesson['category_icon'] }} text-gray-300"></i>
                              {{ $lesson['category_label'] }}
                              @if(!empty($lesson['duration'])) &middot; {{ $lesson['duration'] }} min @endif
                            </p>
                          </a>
                          <input type="checkbox"
                                 class="w-5 h-5 rounded border-2 border-gray-400 bg-white text-green-600 focus:ring-green-500 flex-shrink-0"
                                 :checked="isCompleted('{{ $lessonKey }}')"
                                 @change="toggleLesson('{{ $lessonKey }}', $event.target.checked)">
                        </div>
                      @endforeach
                    </div>
                  </div>
                @empty
                  <p class="text-sm text-gray-400 italic">No lessons yet.</p>
                @endforelse
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
                <li class="flex items-start gap-2 text-sm text-gray-600">
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
            <p class="text-sm font-bold text-gray-900 mb-1">{{ $liveshow->title }}</p>
            <p class="text-xs text-gray-400 mb-4 live-session-datetime">{{ $liveshow->start_time->format('l, M j \a\t g:i A') }} &middot; Zoom</p>

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

        {{-- Daily Practice Tracker --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <p class="text-sm font-bold text-gray-900">Daily Practice Tracker</p>
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
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">Lessons Completed</span>
              <span class="font-bold text-gray-900">{{ $stats['lessons_completed'] }}/{{ $stats['lessons_total'] }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">Current Streak</span>
              <span class="font-bold text-gray-900">{{ $stats['current_streak'] }} days</span>
            </div>
            <div class="flex items-center justify-between text-sm pt-2.5 border-t border-gray-100">
              <span class="text-gray-500">Milestone Achieved</span>
              <span class="font-bold text-gray-900">{{ $stats['milestone_name'] }} ({{ $stats['milestone_count'] }})</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500">Current Level</span>
              <span class="font-bold text-violet-600 bg-violet-50 px-2.5 py-1 rounded-md text-[10px]">{{ $stats['current_level'] }}</span>
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

            isCompleted(key) {
                return this.completed.includes(key);
            },

            async toggleLesson(key, checked) {
                if (checked) {
                    if (!this.completed.includes(key)) this.completed.push(key);
                } else {
                    this.completed = this.completed.filter((k) => k !== key);
                }

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
                } catch (error) {
                    console.error('Could not save lesson completion:', error);
                    if (checked) {
                        this.completed = this.completed.filter((k) => k !== key);
                    } else if (!this.completed.includes(key)) {
                        this.completed.push(key);
                    }
                }
            },
        };
    }
</script>

@endsection
