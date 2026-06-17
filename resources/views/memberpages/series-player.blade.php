@extends('layouts.member')

@section('content')

    @php
        // Find current index safely with loose comparison
        $currentIndex = 0;
        if ($activeVideo) {
            $found = $playlist->search(function ($item) use ($activeVideo) {
                return $item->id == $activeVideo->id;
            });
            if ($found !== false) {
                $currentIndex = $found;
            }
        }

        // Get next and previous video securely using values() to reset keys
        $nextVideo = $playlist->values()->get($currentIndex + 1);
        $previousVideo = $currentIndex > 0 ? $playlist->values()->get($currentIndex - 1) : null;

        // Priority: Use explicitly linked related courses if provided, otherwise fallback to playlist neighbors
        if (isset($related_courses) && count($related_courses) > 0) {
            $relatedLessons = collect($related_courses);
        } else {
            // Get related lessons (excluding the active video)
            $otherLessons = $playlist
                ->filter(function ($item) use ($activeVideo) {
                    return $activeVideo ? $item->id != $activeVideo->id : true;
                })
                ->values();

            // Try to get next 3 lessons in sequence, wrap around if needed
            $relatedLessons = $otherLessons->slice($currentIndex, 3);
            if ($relatedLessons->count() < 3) {
                $relatedLessons = $otherLessons->take(3);
            }
        }
    @endphp

    <div class="min-h-screen bg-white">

        {{-- Breadcrumb --}}
        <div class="px-6 py-3 border-b border-gray-100">
            <div class="max-w-[1280px] mx-auto flex items-center gap-1.5 text-[13px] text-gray-500">
                <a href="/home" class="hover:text-[#0FA9A0]">Home</a>
                <span class="text-gray-300 text-xs">›</span>
                @if($series)
                    <a href="{{ route('piano.exercise.musical') }}" class="hover:text-[#0FA9A0]">Musical Application</a>
                @else
                    <a href="{{ route('piano.exercise.finger') }}" class="hover:text-[#0FA9A0]">Finger Exercises</a>
                @endif
                <span class="text-gray-300 text-xs">›</span>
                <a href="{{ url()->current() . '?' . http_build_query(request()->except('video_id')) }}"
                    class="hover:text-[#0FA9A0]">{{ ucfirst($skillLevel) }}</a>
                @if ($activeVideo)
                    <span class="text-gray-300 text-xs">›</span>
                    <span class="text-gray-700 font-medium">{{ Str::title($activeVideo->title) }}</span>
                @endif
            </div>
        </div>

        @if ($activeVideo)

            {{-- Main layout: video+content LEFT, sidebar RIGHT --}}
            <div class="max-w-[1280px] mx-auto px-6 pt-6 pb-16 flex flex-col lg:flex-row gap-8 items-start">

                {{-- ── LEFT COLUMN ── --}}
                <div class="flex-1 min-w-0">

                    {{-- Video Player --}}
                    {{-- <div class="w-full bg-black rounded-xl overflow-hidden aspect-video mb-5 shadow"> --}}
                        <div id="uploads-single" class="w-full h-full"></div>
                    {{-- </div> --}}

                    {{-- Title + Description --}}
                    <h1 class="text-[22px] font-bold text-gray-900 mb-1">
                        {{ Str::title($activeVideo->title) }}
                    </h1>
                    <p class="text-gray-500 text-[14px] leading-relaxed mb-5">
                        {{ $activeVideo->description ?? 'Learn how to use the up-down principle to create smooth, musical embellishments.' }}
                    </p>

                    {{-- Action Buttons --}}
                    <div class="flex items-center flex-wrap gap-3 mb-10">
                        {{-- @if($previousVideo)
                            <a href="{{ request()->fullUrlWithQuery(['video_id' => $previousVideo->id]) }}" 
                               class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-[14px] font-semibold px-5 py-2.5 rounded-lg transition-colors">
                                <i class="fa-solid fa-arrow-left text-[14px]"></i> Previous
                            </a>
                        @endif

                        @if($nextVideo)
                            <a href="{{ request()->fullUrlWithQuery(['video_id' => $nextVideo->id]) }}" 
                               class="flex items-center gap-2 bg-[#0FA9A0]/10 hover:bg-[#0FA9A0]/20 text-[#0FA9A0] text-[14px] font-semibold px-5 py-2.5 rounded-lg transition-colors">
                                Next Lesson <i class="fa-solid fa-arrow-right text-[14px]"></i>
                            </a>
                        @endif --}}

                        <form action="/member/course/{{ $activeVideo->id }}/complete" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 bg-[#0FA9A0] hover:bg-[#0c9088] text-white text-[14px] font-semibold px-5 py-2.5 rounded-lg transition-colors">
                                <i class="fa-regular fa-circle-check text-[15px]"></i>
                                Complete
                            </button>
                        </form>

                        <form action="{{ route('bookmark.toggle') }}" method="POST" class="bookmark-form">
                            @csrf
                            <input type="hidden" name="bookmarkable_id" value="{{ $activeVideo->id }}">
                            <input type="hidden" name="bookmarkable_type" value="uploads">
                            <button type="submit"
                                class="bookmark-btn flex items-center gap-2 text-[14px] font-semibold px-5 py-2.5 rounded-lg border transition-colors
                   {{ $isBookmarked
                       ? 'bg-yellow-50 border-yellow-200 text-yellow-600 hover:bg-yellow-100'
                       : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                                <i class="fa-regular fa-bookmark text-[15px]"></i>
                                {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                            </button>
                        </form>
                    </div>

                    {{-- Related Lessons --}}
                    @if ($relatedLessons->count() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-[17px] font-bold text-gray-900">Related Lessons</h3>
                                {{-- <a href="#" class="text-[#0FA9A0] text-[13px] font-semibold hover:underline">View
                                    All</a> --}}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach ($relatedLessons as $related)
                                    @php
                                        // If it's an Upload, we need to ensure level and skill_level are in the URL
                                        $urlParams = ['video_id' => $related->id];
                                        if (isset($related->level)) {
                                            $urlParams['level'] = $related->level;
                                            $urlParams['skill_level'] = $related->skill_level ?? 'Basic';
                                            unset($urlParams['series']); // Remove series if it was present
                                        }
                                        $relatedLink = route('piano.exercise.player', $urlParams);
                                    @endphp
                                    <a href="{{ $relatedLink }}"
                                        class="group bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">

                                        {{-- Thumbnail --}}
                                        <div class="relative aspect-video bg-black overflow-hidden">
                                            <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}"
                                                class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" />

                                            {{-- Duration badge top-left --}}
                                            <div
                                                class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[11px] font-bold px-2 py-0.5 rounded">
                                                {{ $related->duration ?? '06:00' }}
                                            </div>

                                            {{-- Play button center --}}
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div
                                                    class="w-10 h-10 bg-black/50 backdrop-blur-sm rounded-full flex items-center justify-center group-hover:bg-[#0FA9A0] transition-colors">
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
                                                    class="bg-[#0FA9A0]/10 text-[#0FA9A0] text-[11px] font-bold px-3 py-1 rounded-full">
                                                    {{ ucfirst($skillLevel) }}
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-8">
                        <h2 class="text-[18px] font-bold text-gray-900 mb-6">Discussion</h2>
                        <form action="{{ route('piano.exercise.comment') }}" method="POST" class="mb-8">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $activeVideo->id }}">
                            <input type="hidden" name="category" value="piano exercise">
                            <textarea name="comment" placeholder="What did you learn from this lesson?" 
                                class="w-full bg-gray-50 border border-gray-100 rounded-xl p-4 text-[14px] focus:ring-2 focus:ring-[#0FA9A0] focus:border-transparent transition-all outline-none" rows="3"></textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="bg-[#0FA9A0] text-white font-bold px-6 py-2.5 rounded-xl hover:bg-[#0c9088] transition-all">Comment</button>
                            </div>
                        </form>

                        <div class="space-y-6">
                            @foreach($comments as $comment)
                                <div class="flex gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 bg-[#0FA9A0]/10 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-[#0FA9A0] font-bold text-sm">{{ substr($comment->user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-[14px] font-bold text-gray-900">{{ $comment->user->name }}</p>
                                            <span class="text-[11px] text-gray-400">• {{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-[13px] text-gray-600 leading-relaxed">{{ $comment->comment }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- ── RIGHT COLUMN: Sidebar Playlist ── --}}
                <aside
                    class="w-full lg:w-[360px] flex-shrink-0 border border-gray-100 rounded-xl overflow-hidden shadow-sm sticky top-6 bg-white">

                    {{-- Header --}}
                    <div class="px-5 py-4 border-b border-gray-100">
                        <p class="text-[11px] font-bold text-gray-400 tracking-[0.14em] uppercase">
                            Lessons in this course:
                        </p>
                    </div>

                    {{-- Scrollable list --}}
                    <div class="overflow-y-auto" style="max-height: 520px;">
                        @foreach ($playlist as $item)
                            @php $isActive = $activeVideo && $item->id == $activeVideo->id; @endphp
                            <a href="{{ request()->fullUrlWithQuery(['video_id' => $item->id]) }}"
                                class="flex items-start gap-3 px-5 py-4 border-b border-gray-50 transition-colors
                    {{ $isActive ? 'bg-[#0FA9A0]' : 'bg-white hover:bg-gray-50' }}">

                                {{-- Play icon --}}
                                <div class="shrink-0 mt-0.5">
                                    @if ($isActive)
                                        <i class="fa-solid fa-circle-play text-white text-[18px]"></i>
                                    @else
                                        <i class="fa-regular fa-circle-play text-gray-300 text-[18px]"></i>
                                    @endif
                                </div>

                                {{-- Title + duration --}}
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-[12px] font-bold uppercase tracking-wide leading-snug
                         {{ $isActive ? 'text-white' : 'text-gray-800' }}">
                                        {{ $item->title }}
                                    </p>
                                    <p class="text-[11px] mt-1.5 {{ $isActive ? 'text-teal-100' : 'text-gray-400' }}">
                                        {{ $item->duration ?? '05:00' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Next Lesson button --}}
                    <div class="p-4 bg-white border-t border-gray-100 flex gap-2">
                        @if ($previousVideo)
                            <a href="{{ request()->fullUrlWithQuery(['video_id' => $previousVideo->id]) }}"
                                class="flex items-center justify-center gap-2 w-1/2 py-3 border border-gray-200 rounded-lg text-gray-800 font-bold text-[14px] hover:bg-gray-50 hover:border-gray-300 transition-all">
                                <i class="fa-solid fa-arrow-left text-sm"></i> Prev
                            </a>
                        @endif

                        @if ($nextVideo)
                            <a href="{{ request()->fullUrlWithQuery(['video_id' => $nextVideo->id]) }}"
                                class="flex items-center justify-center gap-2 {{ $previousVideo ? 'w-1/2' : 'w-full' }} py-3 border border-gray-200 rounded-lg text-gray-800 font-bold text-[14px] hover:bg-gray-50 hover:border-gray-300 transition-all">
                                Next <i class="fa-solid fa-arrow-right text-sm"></i>
                            </a>
                        @else
                            <button disabled
                                class="w-full py-3 border border-gray-100 rounded-lg text-gray-400 font-bold text-[14px] bg-gray-50 cursor-not-allowed">
                                Course Completed
                            </button>
                        @endif
                    </div>

                </aside>

            </div>
        @else
            {{-- Empty state --}}
            <div class="max-w-[1280px] mx-auto px-6 py-20 text-center">
                <i class="fa-regular fa-folder-open text-gray-200 text-6xl block mb-5"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">No exercises found</h2>
                <p class="text-gray-400 mb-7">There are currently no exercises available for this level.</p>
                <a href="{{ route('piano.exercise.finger') }}"
                    class="inline-block bg-[#0FA9A0] text-white px-7 py-2.5 rounded-lg font-semibold hover:bg-[#0d928a] transition-colors">
                    Go Back
                </a>
            </div>

        @endif

    </div>

    @if ($activeVideo)
        <script>
            window.uploadData = @json($activeVideo);
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
                        '<i class="fa-regular fa-bookmark text-[15px]"></i> Bookmark Lesson';
                    }
                });
            });
        </script>
    @endif

@endsection
