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

        $completableType = $series ? 'musical_applications' : 'uploads';
        $completableClass = $series ? \App\Models\MusicalApplication::class : \App\Models\Upload::class;

        $isCompleted = $activeVideo && \App\Models\LessonCompletion::where('user_id', auth()->id())
            ->where('completable_id', $activeVideo->id)
            ->where('completable_type', $completableClass)
            ->exists();

        $completedIds = \App\Models\LessonCompletion::where('user_id', auth()->id())
            ->where('completable_type', $completableClass)
            ->pluck('completable_id')
            ->all();
    @endphp

    <div class="min-h-screen bg-white">

        {{-- Breadcrumb --}}
        @php
            $fingerLevelLabels = [
                'independence' => 'Hand Independence',
                'flexibility' => 'Hand Flexibility',
                'dexterity' => 'Hand Dexterity',
                'strength' => 'Finger Strength',
            ];
            $courseLabel = $series ?: ($fingerLevelLabels[$level] ?? Str::title($level));
        @endphp
        <section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center gap-x-2 gap-y-1 min-h-8 text-sm text-gray-500">
                <a href="/home" class="hover:text-gray-700">Dashboard</a>
                <span>/</span>
                @if($series)
                    <a href="{{ route('piano.exercise.musical') }}" class="hover:text-gray-700">Guided Practice</a>
                @else
                    <a href="{{ route('piano.exercise.finger') }}" class="hover:text-gray-700">Finger Exercises</a>
                @endif
                @if ($courseLabel)
                    <span>/</span>
                    <span class="text-blue-600 font-medium">{{ $courseLabel }}</span>
                @endif
            </div>
        </section>

        @if ($activeVideo)

            {{-- Main layout: video+content LEFT, sidebar RIGHT --}}
            <div class="max-w-[1280px] mx-auto px-6 pt-6 pb-16 flex flex-col lg:flex-row gap-8 items-start">

                {{-- ── LEFT COLUMN ── --}}
                <div class="flex-1 min-w-0 w-full">

                    {{-- Video Player --}}
                    {{-- <div class="w-full bg-black rounded-xl overflow-hidden aspect-video mb-5 shadow"> --}}
                        <div id="uploads-single" class="w-full h-full"></div>
                    {{-- </div> --}}

                    {{-- Title --}}
                    <h1 class="text-[22px] font-bold text-gray-900 mt-5 mb-5">
                        {{ Str::title($activeVideo->title) }}
                    </h1>

                    @include('memberpages.partials.midi-practice-display', [
                        'midiPracticeFile' => $midiPracticeFile,
                        'midiPracticeFiles' => $midiPracticeFiles,
                        'midiPracticeTitle' => Str::title($activeVideo->title),
                    ])

                    @if (!empty($activeVideo->images) && is_array($activeVideo->images))
                        <div class="mt-6 mb-8">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-[0.14em] mb-4">Course Walkthrough / Highlights</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($activeVideo->images as $imgPath)
                                    <div class="rounded-xl overflow-hidden border border-gray-100 shadow-sm bg-gray-50 aspect-video relative group">
                                        <img src="{{ asset($imgPath) }}" alt="Walkthrough Image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


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
                               class="flex items-center gap-2 bg-[#2563EB]/10 hover:bg-[#2563EB]/20 text-[#2563EB] text-[14px] font-semibold px-5 py-2.5 rounded-lg transition-colors">
                                Next Lesson <i class="fa-solid fa-arrow-right text-[14px]"></i>
                            </a>
                        @endif --}}

                        <form action="{{ route('bookmark.toggle') }}" method="POST" class="bookmark-form">
                            @csrf
                            <input type="hidden" name="bookmarkable_id" value="{{ $activeVideo->id }}">
                            <input type="hidden" name="bookmarkable_type" value="{{ $completableType }}">
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
                            <input type="hidden" name="completable_id" value="{{ $activeVideo->id }}">
                            <input type="hidden" name="completable_type" value="{{ $completableType }}">
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

                    {{-- Lessons in this course (mobile & tablet only) --}}
                    <div class="lg:hidden mb-10">
                        @include($level ? 'memberpages.partials.lesson-playlist-grouped' : 'memberpages.partials.lesson-playlist')
                    </div>

                    <div class="mt-10 pt-8 border-t border-gray-100" id="discussion-section" data-course-id="{{ $activeVideo->id }}" data-comment-category="piano exercise">
                        <h2 class="text-[17px] font-semibold text-gray-900 tracking-tight mb-5">Discussion</h2>
                        <form id="comment-form" class="mb-8">
                            <textarea name="comment" placeholder="What did you learn from this lesson?"
                                class="w-full bg-white border border-gray-200 rounded-2xl px-4 py-3.5 text-[14px] text-gray-800 placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none resize-none" rows="3"></textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="bg-gray-900 text-white text-[13px] font-semibold px-5 py-2 rounded-full hover:bg-black transition-colors">Comment</button>
                            </div>
                        </form>

                        <div class="divide-y divide-gray-100" id="comment-list">
                            @foreach($comments as $comment)
                                @include('memberpages.partials.course-video-comment', ['comment' => $comment])
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- ── RIGHT COLUMN: Sidebar Playlist (desktop only) ── --}}
                <aside class="hidden lg:block w-[360px] flex-shrink-0 sticky top-6">
                    @include($level ? 'memberpages.partials.lesson-playlist-grouped' : 'memberpages.partials.lesson-playlist')
                </aside>

            </div>
        @else
            {{-- Empty state --}}
            <div class="max-w-[1280px] mx-auto px-6 py-20 text-center">
                <i class="fa-regular fa-folder-open text-gray-200 text-6xl block mb-5"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">No exercises found</h2>
                <p class="text-gray-400 mb-7">There are currently no exercises available for this level.</p>
                <a href="{{ route('piano.exercise.finger') }}"
                    class="inline-block bg-[#2563EB] text-white px-7 py-2.5 rounded-lg font-semibold hover:bg-[#1D4ED8] transition-colors">
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
                        console.error('Failed to mark course as completed:', err);
                        btn.disabled = false;
                    }
                });
            });

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

        @include('memberpages.partials.course-video-comment-script')
    @endif

@endsection
