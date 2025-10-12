@extends('layouts.member')

@section('content')
<section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-6 px-4">
  <div class="max-w-7xl mx-auto space-y-3">
    <!-- Breadcrumb -->
    <div class="flex justify-between items-center">
      <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="/home" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="/member/{{ Str::slug($lesson->category) }}" class="hover:text-blue-600 font-semibold">
          {{ Str::title($lesson->category) }} /
        </a>
        <a href="/member/lesson/{{ $lesson->id }}" class="hover:text-blue-600 font-semibold">
          {{ Str::title($lesson->title) }}
        </a>
      </div>
      <div class="flex items-center space-x-2">
        <i class="fa fa-user-circle text-xl"></i>
      </div>
    </div>
  </div>
</section>

<section class="flex items-start justify-center min-h-screen bg-gray-100 p-6">
  <!-- Main Content -->
  <div class="bg-white max-w-3xl w-full rounded-lg shadow-lg p-4 md:p-6 space-y-6">
    <!-- Video Section -->
        <div class="w-full rounded-lg overflow-hidden">
      <div class="relative w-full pb-[75%] sm:pb-[65%] md:pb-[56.25%] h-0">
        <div class="absolute top-0 left-0 w-full h-full">
          {!! $lesson->video_url !!}
        </div>
      </div>
    </div>

  <style>
    iframe, video {
      width: 100% !important;
      height: 100% !important;
      border-radius: 0.5rem;
    }
  </style>


    <!-- Title + Bookmark -->
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-800">
        {{ Str::title($lesson->title) }}
      </h1>

      <!-- Bookmark Toggle -->
      <form action="{{ route('bookmark.toggle') }}" method="POST" class="inline bookmark-form">
        @csrf
        <input type="hidden" name="video_id" value="{{ $lesson->id }}">
        <input type="hidden" name="source" value="uploads">

        <button type="submit" 
          class="bookmark-btn flex items-center px-3 py-1.5 border rounded-lg text-sm 
                 {{ $isBookmarked ? 'bg-yellow-100 border-yellow-500 text-yellow-600' : 'bg-gray-100 border-gray-300 text-gray-600' }}">
          <i class="fa fa-bookmark mr-2"></i>
          {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
        </button>
      </form>
    </div>

    <!-- Comments -->
    <div class="mt-6">
      <h2 class="text-lg font-semibold mb-4">Comments</h2>
      <div id="comment-section" class="space-y-4">
        @foreach ($comments as $comment)
          <div class="flex items-start space-x-3">
            <i class="fa fa-user-circle text-gray-500 text-2xl"></i>
            <div class="bg-gray-100 rounded-lg px-4 py-2 w-full">
              <p class="text-gray-800 text-sm">
                <span class="font-semibold">{{ $comment->user->first_name }}:</span> "{{ $comment->comment }}"
              </p>
              <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Sidebar -->
  <aside class="hidden md:flex w-64 bg-white rounded-lg shadow-lg pl-4 pr-2 py-4 ml-6 flex-col gap-8">
    <div class="flex flex-col gap-8 w-full">
      <!-- Related Courses -->
      <div class="border-b pb-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Related Courses</h3>
        @if ($relatedUploads->isNotEmpty())
          <ul class="space-y-2">
            @foreach ($relatedUploads as $item)
              <li>
                <a href="/member/lesson/{{ $item->id }}" class="text-blue-600 hover:underline">
                  {{ $item->title }}
                </a>
              </li>
            @endforeach
          </ul>
        @else
          <p class="text-gray-500 text-sm">No related course available.</p>
        @endif
      </div>
      <!-- Latest Comments -->
      <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Latest Comments</h3>
        <ul class="space-y-3">
          @foreach ($comments->take(5) as $comment)
            <li class="flex items-start space-x-3">
              <i class="fa fa-user-circle text-gray-500 text-xl"></i>
              <div>
                <p class="text-gray-800 text-sm">
                  <span class="font-semibold">{{ $comment->user->first_name }}:</span> "{{ $comment->comment }}"
                </p>
                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
              </div>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </aside>
</section>

<script>
  // Optional AJAX toggle
  document.querySelectorAll('.bookmark-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      let res = await fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: { 'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value }
      });
      let json = await res.json();
      let btn = this.querySelector('.bookmark-btn');
      if (json.status === 'added') {
        btn.classList.remove('bg-gray-100','border-gray-300','text-gray-600');
        btn.classList.add('bg-yellow-100','border-yellow-500','text-yellow-600');
        btn.innerHTML = '<i class="fa fa-bookmark mr-2"></i> Bookmarked';
      } else {
        btn.classList.remove('bg-yellow-100','border-yellow-500','text-yellow-600');
        btn.classList.add('bg-gray-100','border-gray-300','text-gray-600');
        btn.innerHTML = '<i class="fa fa-bookmark mr-2"></i> Bookmark';
      }
    });
  });
</script>
@endsection
