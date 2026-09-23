<section class="max-w-7xl mx-auto px-4 py-6
    grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

  <!-- Card 1: Courses -->
  <div class="min-h-[190px] h-full flex flex-col p-6 bg-[#F3F5F6] rounded-lg shadow-sm border border-gray-100">
    <div class="w-11 h-11 flex items-center justify-center bg-[#E8EDF2] rounded-xl flex-shrink-0 mb-3">
      <i class="fa fa-graduation-cap text-[#435065] text-lg"></i>
    </div>
    <h4 class="font-semibold text-[#435065] text-[18px] font-sf">Courses</h4>
    <p class="text-sm text-[#5E6779] mt-1 font-sf flex-1">Pick a course that matches your skill level</p>
    <a href="{{ route('member.roadmap') }}"
       class="mt-4 inline-flex items-center justify-center gap-1.5 w-full py-2.5 rounded-lg border border-blue-200 bg-white text-blue-600 text-sm font-semibold font-sf hover:bg-blue-50 transition-colors">
      View Courses
      <i class="fa fa-angle-right text-xs"></i>
    </a>
  </div>

  <!-- Card 2: Community -->
  <div class="min-h-[190px] h-full flex flex-col p-6 bg-[#F3F5F6] rounded-lg shadow-sm border border-gray-100">
    <div class="w-11 h-11 flex items-center justify-center bg-indigo-50 rounded-xl flex-shrink-0 mb-3">
      <img src="/images/community.svg" class="w-6 h-6 object-contain" />
    </div>
    <h4 class="font-semibold text-[#435065] text-[18px] font-sf">Community</h4>
    <p class="text-sm text-[#5E6779] mt-1 font-sf flex-1">View the latest community activities</p>
    <a href="/member/community/activity-feed"
       class="mt-4 inline-flex items-center justify-center gap-1.5 w-full py-2.5 rounded-lg border border-blue-200 bg-white text-blue-600 text-sm font-semibold font-sf hover:bg-blue-50 transition-colors">
      View Community
      <i class="fa fa-angle-right text-xs"></i>
    </a>
  </div>

  <!-- Card 3: Personalized Roadmap -->
  <div class="relative min-h-[190px] h-full flex flex-col p-6 rounded-2xl border border-amber-400/20 shadow-xl shadow-black/30 overflow-hidden bg-gradient-to-br from-gray-800 via-gray-900 to-black">
    {{-- Subtle gold glow accent --}}
    <div class="pointer-events-none absolute -top-10 -right-10 w-32 h-32 bg-amber-400/20 rounded-full blur-3xl"></div>

    <div class="relative w-11 h-11 flex items-center justify-center bg-white/5 border border-white/10 rounded-xl flex-shrink-0 mb-3">
      <i class="fa fa-route text-amber-300 text-lg"></i>
    </div>
    <h4 class="relative font-semibold text-white text-[18px] font-sf">Personalized Roadmap</h4>
    <p class="relative text-sm text-gray-400 mt-1 font-sf flex-1">Your custom path to mastering piano</p>
    <a href="{{ route('member.personalized-plan') }}"
       class="relative mt-4 inline-flex items-center justify-center gap-1.5 w-full py-2.5 rounded-lg bg-gradient-to-r from-amber-500 to-amber-300 text-black text-sm font-bold font-sf shadow-md shadow-amber-500/30 hover:shadow-amber-500/50 hover:brightness-105 transition-all">
      View Personalized Plan
      <i class="fa fa-angle-right text-xs"></i>
    </a>
  </div>

</section>
