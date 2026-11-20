<section class="max-w-7xl mx-auto grid grid-cols-1 gap-4 px-4 py-6 lg:grid-cols-5">

  <div class="rounded-xl border border-blue-200 bg-blue-50 p-5 shadow-sm sm:p-6 lg:col-span-3">
    <div class="flex items-start gap-4">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#1447A6] text-white">
        <i class="fa {{ $resumeLesson ? 'fa-circle-play' : 'fa-graduation-cap' }} text-lg" aria-hidden="true"></i>
      </div>
      <div class="min-w-0">
        <p class="text-xs font-bold uppercase tracking-wide text-[#1447A6]">{{ $resumeLesson ? 'Continue Learning' : 'Get Started' }}</p>
        <h2 class="mt-1 break-words text-xl font-bold leading-tight text-gray-900 sm:text-2xl">
          {{ $resumeLesson['title'] ?? 'Start Your Roadmap' }}
        </h2>
        <p class="mt-2 text-sm text-gray-700">
          @if($resumeLesson)
            {{ $resumeLesson['context'] }}
            @if(!empty($resumeLesson['progress']))
              <span aria-hidden="true">&middot;</span>
              {{ $resumeLesson['progress']['completed'] }} of {{ $resumeLesson['progress']['total'] }} lessons complete
            @endif
          @else
            Find your level and follow a step-by-step path through your first lessons.
          @endif
        </p>
      </div>
    </div>
    <a href="{{ $resumeLesson['url'] ?? route('member.roadmap') }}"
       class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1447A6] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#0F3A8A] sm:w-auto">
      {{ $resumeLesson ? 'Resume Lesson' : 'Start Your Roadmap' }}
      <i class="fa fa-angle-right text-xs" aria-hidden="true"></i>
    </a>
  </div>

  <div class="flex flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-2">
    <div class="flex items-start gap-4">
      <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#C85A5A]/10 text-[#A43E3E]">
        <i class="fa fa-bullseye text-lg" aria-hidden="true"></i>
      </div>
      <div class="min-w-0">
        <p class="text-xs font-bold uppercase tracking-wide text-[#A43E3E]">Personalised Path</p>
        @if($nextPractice)
          <h2 class="mt-1 break-words text-lg font-bold text-gray-900">{{ ($nextPractice['name'] ?? null) ?: 'Next practice task' }}</h2>
          <p class="mt-1 text-sm text-gray-600">
            Next up in your plan
            @if(!empty($nextPractice['week'])) &middot; Week {{ $nextPractice['week'] }} @endif
            @if(!empty($nextPractice['duration'])) &middot; {{ $nextPractice['duration'] }} min @endif
          </p>
        @elseif($planPending)
          <h2 class="mt-1 text-lg font-bold text-gray-900">Your plan is in progress</h2>
          <p class="mt-1 text-sm text-gray-600">Check here for your next practice task once your plan is ready.</p>
        @elseif($personalizedPlan && $personalizedPlan->allLessonKeys())
          <h2 class="mt-1 text-lg font-bold text-gray-900">You completed your practice plan</h2>
          <p class="mt-1 text-sm text-gray-600">Open your plan to review the lessons you worked through.</p>
        @else
          <h2 class="mt-1 text-lg font-bold text-gray-900">Your 90-day practice plan</h2>
          <p class="mt-1 text-sm text-gray-600">Get lessons and practice tasks chosen for your skill level and goals.</p>
        @endif
      </div>
    </div>

    <div class="mt-auto flex flex-wrap items-center gap-4 pt-5">
      @if(!empty($nextPractice['url']))
        <a href="{{ $nextPractice['url'] }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#1447A6] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#0F3A8A]">
          Start Practice
          <i class="fa fa-angle-right text-xs" aria-hidden="true"></i>
        </a>
      @endif
      <a href="{{ route('member.personalized-plan') }}" class="text-sm font-semibold text-[#1447A6] underline-offset-4 hover:underline">
        {{ $planPending ? 'Check Plan Status' : 'View Plan' }}
      </a>
    </div>
  </div>
</section>
