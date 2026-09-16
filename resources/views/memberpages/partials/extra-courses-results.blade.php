<!-- Course Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  @forelse($categories as $cat)
    @php
      $firstCourse = $cat->courses->first();
      $lessonCount = $cat->courses->count();
      $level       = $cat->level;
      $levelLabel  = $levelLabels[$level] ?? ucfirst($level);
      $levelStyle  = $levelStyles[$level] ?? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400';
      $isNew       = \App\Models\LessonView::anyNewUnviewed(auth()->id(), $cat->courses);
    @endphp

    <div
      class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col group">

      <!-- Thumbnail -->
      <a href="/member/lesson/{{ $firstCourse->id }}?type=extra_course" class="block relative overflow-hidden" style="aspect-ratio:16/9;">
        @if($firstCourse->thumbnail_url)
          <img src="{{ $firstCourse->thumbnail_url }}" alt="{{ $cat->category }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        @else
          <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-900 to-purple-900">
            <i class="fa fa-music text-5xl text-white/30"></i>
          </div>
        @endif

        @if($isNew)
          <div class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded-md tracking-wide">
            NEW
          </div>
        @endif

        <!-- Lesson count badge -->
        <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-md">
          {{ $lessonCount }} {{ Str::plural('Lesson', $lessonCount) }}
        </div>

        <!-- Play overlay -->
        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
          <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-xl scale-90 group-hover:scale-100 transition duration-300">
            <i class="fa fa-play text-black text-sm ml-0.5"></i>
          </div>
        </div>
      </a>

      <!-- Card Body -->
      <div class="p-5 flex flex-col gap-3 flex-1">
        <h3 class="text-[15px] font-bold text-gray-900 dark:text-white leading-snug">
          {{ $cat->category }}
        </h3>

        <!-- Level Badge -->
        <div>
          <span class="inline-flex items-center gap-1.5 {{ $levelStyle }} text-xs font-semibold px-2.5 py-1 rounded-full">
            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
            {{ $levelLabel }}
          </span>
        </div>

        <!-- Watch Now Button -->
        <a href="/member/lesson/{{ $firstCourse->id }}?type=extra_course"
           class="mt-auto flex items-center justify-center w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all duration-200">
          Watch Now
        </a>
      </div>
    </div>

  @empty
    <div class="col-span-full text-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100">
      <i class="fa fa-graduation-cap text-5xl text-gray-200 mb-4 block"></i>
      @if($search)
        <p class="text-gray-500 font-medium">No courses found for "{{ $search }}".</p>
      @else
        <p class="text-gray-500 font-medium">No courses found for this level.</p>
      @endif
    </div>
  @endforelse
</div>

@if ($categories->total() > 0)
  <p class="text-center text-xs text-gray-400 mt-6">
    Showing {{ $categories->firstItem() }}&ndash;{{ $categories->lastItem() }} of {{ $categories->total() }} {{ Str::plural('result', $categories->total()) }}
  </p>
@endif

@if ($categories->hasPages())
  <div class="flex justify-center py-4">
    {{ $categories->links('components.pagination') }}
  </div>
@endif
