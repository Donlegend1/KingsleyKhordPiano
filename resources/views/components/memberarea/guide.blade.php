<section class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-2 gap-4">

  {{-- Card 1: Get Started / Resume Lesson --}}
  <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-5 rounded-xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-start gap-4 flex-1 min-w-0">
      <div class="flex items-center justify-center w-12 h-12 flex-shrink-0 rounded-xl bg-[#1447A6]/10">
        <i class="fa {{ $resumeLesson ? 'fa-circle-play' : 'fa-graduation-cap' }} text-[#1447A6] text-lg"></i>
      </div>
      <div class="flex-1 min-w-0">
        <span class="inline-block text-[10px] font-bold uppercase tracking-wide text-[#1447A6] bg-[#1447A6]/10 px-2 py-0.5 rounded-full font-sf">
          {{ $resumeLesson ? 'Continue Learning' : 'Get Started' }}
        </span>
        <h3 class="font-bold text-gray-900 text-[15px] mt-1.5 font-sf">
          {{ $resumeLesson ? 'Pick Up Where You Left Off' : 'Start Your Learning Journey' }}
        </h3>
        <p class="text-sm text-gray-500 mt-0.5 font-sf">
          @if($resumeLesson)
            Continue "{{ $resumeLesson['title'] }}" right where you paused.
          @else
            Explore our courses and take your first lesson.
          @endif
        </p>
      </div>
    </div>
    <a href="{{ $resumeLesson ? $resumeLesson['url'] : route('member.roadmap') }}"
       class="flex-shrink-0 inline-flex items-center justify-center gap-1.5 w-full sm:w-auto bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors font-sf">
      {{ $resumeLesson ? 'Resume' : 'Start' }}
      <i class="fa fa-angle-right text-xs"></i>
    </a>
  </div>

  {{-- Card 2: Personalised Path --}}
  <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-5 rounded-xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-start gap-4 flex-1 min-w-0">
      <div class="flex items-center justify-center w-12 h-12 flex-shrink-0 rounded-xl bg-[#C85A5A]/10">
        <i class="fa fa-bullseye text-[#C85A5A] text-lg"></i>
      </div>
      <div class="flex-1 min-w-0">
        <span class="inline-block text-[10px] font-bold uppercase tracking-wide text-[#C85A5A] bg-[#C85A5A]/10 px-2 py-0.5 rounded-full font-sf">
          Custom Path
        </span>
        <h3 class="font-bold text-gray-900 text-[15px] mt-1.5 font-sf">Personalised Path</h3>
        <p class="text-sm text-gray-500 mt-0.5 font-sf">See where your skills stand and get lessons picked just for you.</p>
      </div>
    </div>
    <a href="{{ route('member.personalized-plan') }}"
       class="flex-shrink-0 inline-flex items-center justify-center gap-1.5 w-full sm:w-auto bg-[#C85A5A] hover:bg-[#B54B4B] text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors font-sf">
      View Plan
      <i class="fa fa-angle-right text-xs"></i>
    </a>
  </div>

</section>
