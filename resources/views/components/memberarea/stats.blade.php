<section class="max-w-7xl mx-auto px-4 py-8">

  <div class="flex flex-col gap-6">
    
    {{-- Live Session / Community / Ear Training --}}
    <hr class="border-t border-gray-100">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest font-sf">Explore</h3>
    @php
      $dashboardLiveshow = \App\Models\Liveshow::where('category', 'event')
          ->where('start_time', '>=', now())
          ->where(function ($query) {
              $query->where('access_type', 'all');
              if (Auth::user()->premium) {
                  $query->orWhere('access_type', 'premium');
              }
          })
          ->orderBy('start_time')
          ->first();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

      @if($dashboardLiveshow)
        @php $dashboardSessionUrl = $dashboardLiveshow->zoom_link ?: '/member/live-session'; @endphp
        <div class="relative flex flex-col p-5 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow"
             data-dash-live-session-card data-start-time="{{ $dashboardLiveshow->start_time->toIso8601String() }}">
          <div class="flex items-center justify-between mb-2">
            <span class="flex items-center gap-1.5 text-amber-600 text-[10px] font-bold uppercase tracking-wide">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
              Upcoming Live Event
            </span>
            @if($dashboardLiveshow->access_type === 'premium')
              <span class="text-[9px] font-bold uppercase tracking-wide text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Premium</span>
            @endif
          </div>
          <p class="text-sm font-bold text-gray-900 mb-1 line-clamp-1">{{ $dashboardLiveshow->title }}</p>
          <p class="text-xs text-gray-400 mb-3 dash-live-session-datetime">{{ $dashboardLiveshow->start_time->format('l, M j \a\t g:i A') }} &middot; Zoom</p>

          <div class="grid grid-cols-4 gap-1.5 mb-4">
            <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg py-2 border border-gray-100">
              <span class="text-sm font-bold text-gray-900 tabular-nums dash-live-session-days">0</span>
              <span class="text-[9px] font-semibold text-gray-400 uppercase">Days</span>
            </div>
            <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg py-2 border border-gray-100">
              <span class="text-sm font-bold text-gray-900 tabular-nums dash-live-session-hours">0</span>
              <span class="text-[9px] font-semibold text-gray-400 uppercase">Hours</span>
            </div>
            <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg py-2 border border-gray-100">
              <span class="text-sm font-bold text-gray-900 tabular-nums dash-live-session-minutes">0</span>
              <span class="text-[9px] font-semibold text-gray-400 uppercase">Mins</span>
            </div>
            <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg py-2 border border-gray-100">
              <span class="text-sm font-bold text-gray-900 tabular-nums dash-live-session-seconds">0</span>
              <span class="text-[9px] font-semibold text-gray-400 uppercase">Sec</span>
            </div>
          </div>

          <a href="{{ $dashboardSessionUrl }}"
             class="mt-auto inline-flex items-center justify-center gap-1.5 w-full py-2.5 rounded-lg bg-[#1447A6] hover:bg-[#0F3A8A] text-white text-sm font-semibold font-sf transition-colors">
            {{ $dashboardLiveshow->zoom_link ? 'Join Live Event' : 'View Live Event' }}
            <i class="fa fa-angle-right text-xs"></i>
          </a>
        </div>

        <script>
          (function () {
            const card = document.querySelector('[data-dash-live-session-card]');
            if (!card) return;
            const startTime = new Date(card.dataset.startTime).getTime();

            const tick = () => {
              const diff = startTime - Date.now();
              const clamp = (n) => Math.max(n, 0);
              card.querySelector('.dash-live-session-days').textContent = clamp(Math.floor(diff / 86400000));
              card.querySelector('.dash-live-session-hours').textContent = clamp(Math.floor((diff % 86400000) / 3600000));
              card.querySelector('.dash-live-session-minutes').textContent = clamp(Math.floor((diff % 3600000) / 60000));
              card.querySelector('.dash-live-session-seconds').textContent = clamp(Math.floor((diff % 60000) / 1000));
            };

            tick();
            setInterval(tick, 1000);
          })();
        </script>
      @else
        <div class="flex flex-col items-center justify-center text-center p-5 bg-white rounded-xl border border-gray-100 shadow-sm">
          <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-lg flex-shrink-0 mb-3">
            <i class="fa fa-video text-gray-400 text-base"></i>
          </div>
          <h4 class="font-semibold text-[#435065] text-[16px] font-sf">Live Event</h4>
          <p class="text-sm text-[#5E6779] mt-1 font-sf">No live events scheduled right now</p>
          <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide text-gray-400 bg-gray-50 px-3 py-1.5 rounded-full font-sf">
            Coming Soon
          </span>
        </div>
      @endif

      <div class="flex flex-col items-center text-center p-5 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 flex items-center justify-center bg-indigo-50 rounded-full flex-shrink-0 mb-3">
          <img src="/images/community.svg" class="w-6 h-6 object-contain" />
        </div>
        <h4 class="font-semibold text-[#435065] text-[16px] font-sf">Community</h4>
        <p class="text-sm text-[#5E6779] mt-1 font-sf">View the latest community activities</p>
        <a href="/member/community/activity-feed"
           class="mt-4 inline-flex items-center justify-center gap-1.5 w-full py-2.5 rounded-lg border border-blue-200 bg-white text-blue-600 text-sm font-semibold font-sf hover:bg-blue-50 transition-colors">
          Join Community
          <i class="fa fa-angle-right text-xs"></i>
        </a>
      </div>

      <div class="flex flex-col items-center text-center p-5 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
        <div class="w-12 h-12 flex items-center justify-center bg-emerald-50 rounded-full flex-shrink-0 mb-3">
          <i class="fa fa-ear-listen text-emerald-600 text-lg"></i>
        </div>
        <h4 class="font-semibold text-[#435065] text-[16px] font-sf">Ear Training</h4>
        <p class="text-sm text-[#5E6779] mt-1 font-sf">Sharpen your ear with guided drills</p>
        <a href="{{ route('ear.training') }}"
           class="mt-4 inline-flex items-center justify-center gap-1.5 w-full py-2.5 rounded-lg border border-blue-200 bg-white text-blue-600 text-sm font-semibold font-sf hover:bg-blue-50 transition-colors">
          Start Training
          <i class="fa fa-angle-right text-xs"></i>
        </a>
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
