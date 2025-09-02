@extends("layouts.member")

@extends("layouts.community")

@section("content")

<!-- Header Section -->
<div class="border border-gray-200 dark:border-gray-500">
    <div class="flex justify-between items-center px-10 py-2 bg-white dark:bg-gray-800">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Feed</h1>
        <i class="fa fa-ellipsis-v text-gray-500 dark:text-gray-300" aria-hidden="true"></i>
    </div>
</div>

<div class="overflow-scroll h-screen">
  <!-- Quick Links Navigation -->
<div class="flex justify-between items-center px-10 py-2 bg-white dark:bg-gray-800 border border-t-0 border-gray-200 dark:border-gray-500">
    <div class="flex space-x-6 text-gray-500 dark:text-gray-200 text-sm">
        <a href="/home" class="hover:text-gray-700">🏠 Dashboard</a>
        <a href="/member/roadmap" class="hover:text-gray-700">🗺️ Roadmap</a>
        <a href="//member/plugins" class="hover:text-gray-700">📁 Download</a>
    </div>
</div>

<!-- Main Feed Section -->
<section class="bg-gray-100 dark:bg-gray-900 py-6 px-2 md:px-10 ">
  <div class="flex flex-col lg:flex-row gap-6 ">

    <!-- Left: Main Feed Area -->
    <div class="flex-1 space-y-6">
      <div class=" rounded-lg p-4">
        <div id="post-list"></div>
      </div>
    </div>

    <!-- Right: Recent Activity -->
    <div class="w-full lg:w-[300px]">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
            Recent Activities
        </h2>
        <div>
          <ul class="space-y-3">
    @php
        $activities = \Illuminate\Support\Facades\DB::table('notifications')
            ->latest()
            ->take(10)
            ->get();
    @endphp

    @forelse ($activities as $activity)
        @php
            $data = json_decode($activity->data, true);
            $firstName = $data['first_name'] ?? '';
            $lastName = $data['last_name'] ?? '';
            $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
            $postId = $data['post_id'] ?? null;
        @endphp

        @if($postId)
            <li>
                <a href="/member/post/{{ $postId }}" class="flex items-center space-x-3 hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg transition">
                    <!-- Avatar or Initials -->
                    @if(!empty($data['by_user_avatar']))
                        <img 
                            src="{{ $data['by_user_avatar'] }}" 
                            alt="{{ trim($firstName . ' ' . $lastName) ?: 'User' }}"
                            class="w-8 h-8 rounded-full object-cover"
                        >
                    @else
                        <div class="w-12 h-10 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold text-gray-700">
                            {{ $initials ?: '?' }}
                        </div>
                    @endif

                    <!-- Text & Time -->
                    <div class="flex flex-col">
                        <span class="text-gray-800 dark:text-gray-100 font-semibold">
                            {{ trim($firstName . ' ' . $lastName) ?: 'Someone' }}
                        </span>

                        <span class="text-gray-500 dark:text-gray-400 text-sm">
                            @if($data['type'] === 'post')
                                made a new post
                            @elseif($data['type'] === 'comment')
                                commented on a post
                            @elseif($data['type'] === 'reply')
                                replied to a comment
                            @elseif($data['type'] === 'like')
                                liked a post
                            @else
                                did something
                            @endif
                            • {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                        </span>
                    </div>
                </a>
            </li>
        @endif
    @empty
        <li class="text-sm text-gray-500 dark:text-gray-400">
            No recent activities
        </li>
    @endforelse
</ul>

        </div>
    </div>
</div>


  </div>
</section>

</div>




@endsection

