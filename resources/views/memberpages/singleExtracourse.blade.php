@extends('layouts.member')

@section('content')

    @php
        // Determine the type
        $lessonType = 'uploads';
        if ($lesson instanceof \App\Models\LearnSong) {
            $lessonType = 'learn_songs';
        } elseif ($lesson instanceof \App\Models\ExtraCourse) {
            $lessonType = 'extra_courses';
        }

        // Every lesson link on this page points to the same model as the
        // current lesson (playlist neighbors, related lessons), so a single
        // type disambiguates all of them for the /member/lesson/{id} route.
        $linkType = match ($lessonType) {
            'learn_songs' => 'learn_song',
            'extra_courses' => 'extra_course',
            default => 'upload',
        };

        $isCompleted = $lesson && \App\Models\LessonCompletion::where('user_id', auth()->id())
            ->where('completable_id', $lesson->id)
            ->where('completable_type', get_class($lesson))
            ->exists();

        $completedIds = $lesson ? \App\Models\LessonCompletion::where('user_id', auth()->id())
            ->where('completable_type', get_class($lesson))
            ->pluck('completable_id')
            ->all() : [];

        // Fetch playlist based on category
        $playlist = collect();
        if ($lessonType === 'learn_songs') {
            $playlist = \App\Models\LearnSong::where('learn_song_category_id', $lesson->learn_song_category_id)
                ->orderBy('position')
                ->get();
        } elseif ($lessonType === 'extra_courses') {
            $playlist = \App\Models\ExtraCourse::where('extra_course_category_id', $lesson->extra_course_category_id)
                ->orderBy('position')
                ->get();
        }

        // Find current index safely with loose comparison
        $currentIndex = 0;
        if ($lesson) {
            $found = $playlist->search(function ($item) use ($lesson) {
                return $item->id == $lesson->id;
            });
            if ($found !== false) {
                $currentIndex = $found;
            }
        }

        // Get next and previous video securely using values() to reset keys
        $nextVideo = $playlist->values()->get($currentIndex + 1);
        $previousVideo = $currentIndex > 0 ? $playlist->values()->get($currentIndex - 1) : null;

        // Priority: Use explicitly linked related courses if provided, otherwise fallback to playlist neighbors
        if (isset($relatedUploads) && $relatedUploads->count() > 0) {
            $relatedLessons = $relatedUploads;
        } else {
            // Get related lessons (excluding the active video) from the playlist
            $otherLessons = $playlist
                ->filter(function ($item) use ($lesson) {
                    return $lesson ? $item->id != $lesson->id : true;
                })
                ->values();

            // Try to get next 3 lessons in sequence, wrap around if needed
            $relatedLessons = $otherLessons->slice($currentIndex, 3);
            if ($relatedLessons->count() < 3 && $otherLessons->count() > 0) {
                $relatedLessons = $relatedLessons->merge($otherLessons->take(3 - $relatedLessons->count()))->unique('id');
            }
        }

        // Fallback: If still empty (no playlist and no tags), get other uploads in the same category
        if ($relatedLessons->isEmpty()) {
            if ($lessonType === 'learn_songs') {
                $relatedLessons = \App\Models\LearnSong::where('level', $lesson->level)
                    ->where('id', '!=', $lesson->id)
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            } elseif ($lessonType === 'extra_courses') {
                $relatedLessons = \App\Models\ExtraCourse::where('level', $lesson->level)
                    ->where('id', '!=', $lesson->id)
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            } else {
                $relatedLessons = \App\Models\Upload::where('category', $lesson->category)
                    ->where('id', '!=', $lesson->id)
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            }
        }

        // Get comments specifically for this lesson
        $lessonComments = \App\Models\CourseVideoComment::where('course_id', $lesson->id)
            ->where('category', 'others')
            ->with(['user'])
            ->latest()
            ->get();
    @endphp

    <div class="min-h-screen bg-white">

        {{-- Breadcrumb --}}
        <section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center gap-x-2 gap-y-1 min-h-8 text-sm text-gray-500">
                <a href="/home" class="hover:text-gray-700">Dashboard</a>
                <span>/</span>
                @if ($lessonType === 'learn_songs')
                    <a href="{{ route('learn.songs') }}" class="hover:text-gray-700">Learn Songs</a>
                @else
                    <a href="{{ route('extra.courses') }}" class="hover:text-gray-700">Extra Courses</a>
                @endif
                @if ($lesson?->category)
                    <span>/</span>
                    <span class="text-[#6366F1] font-medium">{{ $lesson->category->category }}</span>
                @endif
            </div>
        </section>

        @if ($lesson)

            {{-- Main layout: video+content LEFT, sidebar RIGHT --}}
            <div class="max-w-[1280px] mx-auto px-6 pt-6 pb-16 flex flex-col lg:flex-row gap-8 items-start">

                {{-- ── LEFT COLUMN ── --}}
                <div class="flex-1 min-w-0 w-full">

                    {{-- Video Player --}}
                    <div id="uploads-single" class="w-full h-full"></div>

                    {{-- Title --}}
                    <h1 class="text-[22px] font-bold text-gray-900 mt-5 mb-5">
                        {{ Str::title($lesson->title) }}
                    </h1>

                    @if (!empty($lesson->images) && is_array($lesson->images))
                        <div class="mt-6 mb-8">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-[0.14em] mb-4">Course Walkthrough / Highlights</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($lesson->images as $imgPath)
                                    <div class="rounded-xl overflow-hidden border border-gray-100 shadow-sm bg-gray-50 aspect-video relative group">
                                        <img src="{{ asset($imgPath) }}" alt="Walkthrough Image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    {{-- Action Buttons --}}
                    <div class="flex items-center flex-wrap gap-3 mb-10">
                        <form action="{{ route('bookmark.toggle') }}" method="POST" class="bookmark-form">
                            @csrf
                            <input type="hidden" name="bookmarkable_id" value="{{ $lesson->id }}">
                            <input type="hidden" name="bookmarkable_type" value="{{ $lessonType }}">
                            <button type="submit"
                                class="bookmark-btn flex items-center gap-2 text-[14px] font-semibold px-5 py-2.5 rounded-lg border transition-colors
                                {{ $isBookmarked
                                    ? 'bg-yellow-50 border-yellow-200 text-yellow-600 hover:bg-yellow-100'
                                    : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                <i class="fa-regular fa-bookmark text-[15px]"></i>
                                {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                            </button>
                        </form>

                        <form action="{{ route('lesson.complete') }}" method="POST" class="complete-form">
                            @csrf
                            <input type="hidden" name="completable_id" value="{{ $lesson->id }}">
                            <input type="hidden" name="completable_type" value="{{ $lessonType }}">
                            <button type="submit" {{ $isCompleted ? 'disabled' : '' }}
                                class="complete-btn flex items-center gap-2 text-[14px] font-semibold px-5 py-2.5 rounded-lg border transition-colors
                                {{ $isCompleted
                                    ? 'bg-green-50 border-green-200 text-green-600 cursor-not-allowed'
                                    : 'bg-black border-black text-white hover:bg-gray-800' }}">
                                <i class="fa-solid fa-check text-[15px]"></i>
                                {{ $isCompleted ? 'Completed' : 'Mark as Complete' }}
                            </button>
                        </form>
                    </div>

                    {{-- Lessons in this course (Mobile Only) --}}
                    @if ($playlist->count() > 1)
                        <div class="block lg:hidden mb-10 border border-gray-100 rounded-xl overflow-hidden shadow-sm bg-white">
                            {{-- Header --}}
                            <div class="px-5 py-4 border-b border-gray-100">
                                <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase">
                                    Lessons in this course:
                                </p>
                            </div>

                            {{-- Scrollable list --}}
                            <div class="overflow-y-auto" style="max-height: 300px;">
                                @foreach ($playlist as $item)
                                    @php
                                        $isActive = $item->id == $lesson->id;
                                        $itemIsNew = $item->created_at
                                            && $item->created_at->gt(now()->subDays(7))
                                            && !\App\Models\LessonView::hasViewed(auth()->id(), $item);
                                    @endphp
                                    <a href="/member/lesson/{{ $item->id }}?type={{ $linkType }}"
                                        class="flex items-center gap-3 px-5 py-4 border-b border-gray-50 transition-colors
                                        {{ $isActive ? 'bg-blue-50' : 'bg-white hover:bg-gray-50' }}">

                                        {{-- Title --}}
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-[12px] font-bold uppercase tracking-wide leading-snug flex items-center gap-2
                                                {{ $isActive ? 'text-blue-700' : 'text-gray-800' }}">
                                                <span class="truncate">{{ $item->title }}</span>
                                                @if ($itemIsNew)
                                                    <span class="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md tracking-wide flex-shrink-0">NEW</span>
                                                @endif
                                            </p>
                                        </div>
                                        @if (in_array($item->id, $completedIds))
                                            <i class="fa-solid fa-circle-check text-green-500 text-sm flex-shrink-0"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>

                            {{-- Next/Prev Lesson buttons --}}
                            <div class="p-4 bg-white border-t border-gray-100 flex gap-2">
                                @if ($previousVideo)
                                    <a href="/member/lesson/{{ $previousVideo->id }}?type={{ $linkType }}"
                                        class="flex items-center justify-center gap-2 w-1/2 py-3 border border-gray-200 rounded-lg text-gray-800 font-bold text-[14px] hover:bg-blue-50 hover:border-blue-200 transition-all">
                                        <i class="fa-solid fa-arrow-left text-sm"></i> Prev
                                    </a>
                                @endif

                                @if ($nextVideo)
                                    <a href="/member/lesson/{{ $nextVideo->id }}?type={{ $linkType }}"
                                        class="flex items-center justify-center gap-2 {{ $previousVideo ? 'w-1/2' : 'w-full' }} py-3 border border-gray-200 rounded-lg text-gray-800 font-bold text-[14px] hover:bg-blue-50 hover:border-blue-200 transition-all">
                                        Next <i class="fa-solid fa-arrow-right text-sm"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Related Lessons (Only visible at the bottom if playlist is in sidebar) --}}
                    @if ($playlist->count() > 1 && $relatedLessons->count() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-[17px] font-bold text-gray-900">Related Lessons</h3>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($relatedLessons as $related)
                                    @php
                                        $relatedLink = "/member/lesson/{$related->id}?type={$linkType}";
                                        $relatedIsNew = $related->created_at
                                            && $related->created_at->gt(now()->subDays(7))
                                            && !\App\Models\LessonView::hasViewed(auth()->id(), $related);
                                    @endphp
                                    <a href="{{ $relatedLink }}"
                                        class="group bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">

                                        {{-- Thumbnail --}}
                                        <div class="relative aspect-video bg-black overflow-hidden">
                                            <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}"
                                                class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" />

                                            @if ($relatedIsNew)
                                                <div class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded tracking-wide">
                                                    NEW
                                                </div>
                                            @endif

                                            {{-- Duration badge top-left --}}
                                            <div
                                                class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[11px] font-bold px-2 py-0.5 rounded">
                                                {{ $related->duration ?? '05:00' }}
                                            </div>

                                            {{-- Play button center --}}
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div
                                                    class="w-10 h-10 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:bg-[#2563EB] transition-colors">
                                                    <i class="fa-solid fa-play text-white text-xs ml-0.5"></i>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Card text --}}
                                        <div class="p-4">
                                            <p
                                                class="text-[13px] font-semibold text-gray-900 leading-snug line-clamp-2 mb-3 text-center">
                                                {{ Str::title($related->title) }}
                                            </p>
                                            <div class="flex justify-center">
                                                <span
                                                    class="bg-[#2563EB]/10 text-[#2563EB] text-[11px] font-bold px-3 py-1 rounded-full">
                                                    {{ ucfirst($related->level ?? $lesson->level ?? 'Basic') }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Discussion / Comments --}}
                    <div class="mt-8" id="discussion-section" data-course-id="{{ $lesson->id }}" data-comment-category="others">
                        <h2 class="text-[18px] font-bold text-gray-900 mb-6">Discussion</h2>
                        <form id="comment-form" class="mb-8">
                            <textarea name="comment" placeholder="What did you learn from this lesson?"
                                class="w-full bg-gray-50 border border-gray-100 rounded-xl p-4 text-[14px] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all outline-none" rows="3"></textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="bg-[#2563EB] text-white font-bold px-6 py-2.5 rounded-xl hover:bg-[#1D4ED8] transition-all">Comment</button>
                            </div>
                        </form>

                        <div class="space-y-6" id="comment-list">
                            @foreach($lessonComments as $comment)
                                @include('memberpages.partials.course-video-comment', ['comment' => $comment])
                            @endforeach
                        </div>
                    </div>

                    @include('memberpages.partials.course-resources', ['lesson' => $lesson])

                </div>

                {{-- ── RIGHT COLUMN: Sidebar (Playlist or Related Lessons) ── --}}
                <aside
                    class="w-full lg:w-[360px] flex-shrink-0 border border-gray-100 rounded-xl overflow-hidden shadow-sm sticky top-6 bg-white {{ $playlist->count() > 1 ? 'hidden lg:block' : '' }}">

                    @if ($playlist->count() > 1)
                        {{-- Header --}}
                        <div class="px-5 py-4 border-b border-gray-100">
                            <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase">
                                Lessons in this course:
                            </p>
                        </div>

                        {{-- Scrollable list --}}
                        <div class="overflow-y-auto" style="max-height: 520px;">
                            @foreach ($playlist as $item)
                                @php
                                    $isActive = $item->id == $lesson->id;
                                    $itemIsNew = $item->created_at
                                        && $item->created_at->gt(now()->subDays(7))
                                        && !\App\Models\LessonView::hasViewed(auth()->id(), $item);
                                @endphp
                                <a href="/member/lesson/{{ $item->id }}?type={{ $linkType }}"
                                    class="flex items-center gap-3 px-5 py-4 border-b border-gray-50 transition-colors
                                    {{ $isActive ? 'bg-blue-50' : 'bg-white hover:bg-gray-50' }}">

                                    {{-- Title --}}
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-[12px] font-bold uppercase tracking-wide leading-snug flex items-center gap-2
                                            {{ $isActive ? 'text-blue-700' : 'text-gray-800' }}">
                                            <span class="truncate">{{ $item->title }}</span>
                                            @if ($itemIsNew)
                                                <span class="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md tracking-wide flex-shrink-0">NEW</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if (in_array($item->id, $completedIds))
                                        <i class="fa-solid fa-circle-check text-green-500 text-sm flex-shrink-0"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        {{-- Next Lesson button --}}
                        <div class="p-4 bg-white border-t border-gray-100 flex gap-2">
                            @if ($previousVideo)
                                <a href="/member/lesson/{{ $previousVideo->id }}?type={{ $linkType }}"
                                    class="flex items-center justify-center gap-2 w-1/2 py-3 border border-gray-200 rounded-lg text-gray-800 font-bold text-[14px] hover:bg-blue-50 hover:border-blue-200 transition-all">
                                    <i class="fa-solid fa-arrow-left text-sm"></i> Prev
                                </a>
                            @endif

                            @if ($nextVideo)
                                <a href="/member/lesson/{{ $nextVideo->id }}?type={{ $linkType }}"
                                    class="flex items-center justify-center gap-2 {{ $previousVideo ? 'w-1/2' : 'w-full' }} py-3 border border-gray-200 rounded-lg text-gray-800 font-bold text-[14px] hover:bg-blue-50 hover:border-blue-200 transition-all">
                                    Next <i class="fa-solid fa-arrow-right text-sm"></i>
                                </a>
                            @endif
                        </div>
                    @else
                        {{-- Header --}}
                        <div class="px-5 py-4 border-b border-gray-100">
                            <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase">
                                Related Lessons:
                            </p>
                        </div>

                        {{-- Scrollable list --}}
                        <div class="overflow-y-auto" style="max-height: 520px;">
                            @foreach ($relatedLessons as $item)
                                @php
                                    $itemIsNew = $item->created_at
                                        && $item->created_at->gt(now()->subDays(7))
                                        && !\App\Models\LessonView::hasViewed(auth()->id(), $item);
                                @endphp
                                <a href="/member/lesson/{{ $item->id }}?type={{ $linkType }}"
                                    class="flex items-start gap-3 px-5 py-4 border-b border-gray-50 transition-colors bg-white hover:bg-gray-50">

                                    {{-- Play icon --}}
                                    <div class="shrink-0 mt-0.5">
                                        <i class="fa-regular fa-circle-play text-gray-300 text-[18px]"></i>
                                    </div>

                                    {{-- Title + duration --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[12px] font-bold uppercase tracking-wide leading-snug text-gray-800 flex items-center gap-2">
                                            <span class="truncate">{{ $item->title }}</span>
                                            @if ($itemIsNew)
                                                <span class="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md tracking-wide flex-shrink-0">NEW</span>
                                            @endif
                                        </p>
                                        <p class="text-[11px] mt-1.5 text-gray-400">
                                            {{ $item->duration ?? '05:00' }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                </aside>

            </div>
        @else
            {{-- Empty state --}}
            <div class="max-w-[1280px] mx-auto px-6 py-20 text-center">
                <i class="fa-regular fa-folder-open text-gray-200 text-6xl block mb-5"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">No lesson found</h2>
                <p class="text-gray-400 mb-7">There is currently no lesson available.</p>
                <a href="{{ route('extra.courses') }}"
                    class="inline-block bg-[#2563EB] text-white px-7 py-2.5 rounded-lg font-semibold hover:bg-[#1D4ED8] transition-colors">
                    Go Back
                </a>
            </div>

        @endif

    </div>

    @if ($lesson)
        <script>
            window.uploadData = @json($lesson);
        </script>
        <script>
            document.querySelectorAll('.bookmark-form').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const res = await fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value
                        }
                    });
                    const json = await res.json();
                    const btn = this.querySelector('.bookmark-btn');
                    if (json.status === 'added') {
                        btn.classList.remove('bg-white', 'border-gray-200', 'text-gray-700',
                            'hover:bg-gray-50');
                        btn.classList.add('bg-yellow-50', 'border-yellow-200', 'text-yellow-600',
                            'hover:bg-yellow-100');
                        btn.innerHTML = '<i class="fa-regular fa-bookmark text-[15px]"></i> Bookmarked';
                    } else {
                        btn.classList.remove('bg-yellow-50', 'border-yellow-200', 'text-yellow-600',
                            'hover:bg-yellow-100');
                        btn.classList.add('bg-white', 'border-gray-200', 'text-gray-700',
                            'hover:bg-gray-50');
                        btn.innerHTML =
                        '<i class="fa-regular fa-bookmark text-[15px]"></i> Bookmark';
                    }
                });
            });

            document.querySelectorAll('.complete-form').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = this.querySelector('.complete-btn');
                    if (btn.disabled) return;
                    btn.disabled = true;
                    try {
                        const res = await fetch(this.action, {
                            method: 'POST',
                            body: new FormData(this),
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value
                            }
                        });
                        if (!res.ok) {
                            throw new Error('Request failed with status ' + res.status);
                        }
                        btn.classList.remove('bg-black', 'border-black', 'hover:bg-gray-800', 'text-white');
                        btn.classList.add('bg-green-50', 'border-green-200', 'text-green-600', 'cursor-not-allowed');
                        btn.innerHTML = '<i class="fa-solid fa-check text-[15px]"></i> Completed';
                    } catch (err) {
                        console.error('Failed to mark lesson as completed:', err);
                        btn.disabled = false;
                    }
                });
            });
        </script>

        @include('memberpages.partials.course-video-comment-script')
    @endif

@endsection
