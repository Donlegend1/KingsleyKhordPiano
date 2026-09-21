@extends('layouts.member')

@section('content')

    @php
        $isDriveLink = str_contains($liveshow->recording_url, 'drive.google.com');
        $driveFileId = null;
        if ($isDriveLink) {
            preg_match('/(?:file\/d\/|open\?id=)([a-zA-Z0-9_-]+)/', $liveshow->recording_url, $m);
            $driveFileId = $m[1] ?? null;
        }
    @endphp

    <div class="min-h-screen bg-white">

        {{-- Breadcrumb --}}
        <section class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white py-4 px-4 border-b border-gray-150 dark:border-gray-800">
            <div class="max-w-5xl mx-auto flex flex-wrap items-center gap-x-2 gap-y-1 min-h-8 text-sm text-gray-500">
                <a href="/home" class="hover:text-gray-700">Dashboard</a>
                <span>/</span>
                <a href="/member/live-session" class="hover:text-gray-700">Live Shows</a>
                <span>/</span>
                <span class="text-[#C85A5A] font-medium">{{ $liveshow->title }}</span>
            </div>
        </section>

        <div class="max-w-5xl mx-auto px-6 pt-6 pb-16">

            <h1 class="text-[22px] font-bold text-gray-900 mb-5">
                {{ $liveshow->title }}
            </h1>

            {{-- Video Player --}}
            <div class="relative w-full aspect-video rounded-xl overflow-hidden shadow-lg bg-black mb-8">
                @if ($driveFileId)
                    <iframe
                        src="https://drive.google.com/file/d/{{ $driveFileId }}/preview"
                        class="absolute inset-0 w-full h-full"
                        frameborder="0"
                        allow="autoplay; encrypted-media; fullscreen"
                        allowfullscreen
                    ></iframe>
                    {{-- Blocks Google Drive's built-in "open in new window" icon, which can't be removed from the cross-origin preview UI --}}
                    <div class="absolute top-0 right-0 w-16 h-16"></div>
                @else
                    <video
                        src="{{ $liveshow->recording_url }}"
                        class="absolute inset-0 w-full h-full"
                        controls
                    ></video>
                @endif
            </div>

            <p class="text-sm text-gray-500 mb-10">
                Recorded on {{ \Carbon\Carbon::parse($liveshow->start_time)->format('F j, Y') }}
            </p>

            {{-- Discussion / Comments --}}
            <div class="pt-6 border-t border-gray-200" id="discussion-section" data-course-id="{{ $liveshow->id }}" data-comment-category="liveshow">
                <h2 class="text-[15px] font-semibold text-gray-900 mb-1">Discussion</h2>
                <p class="text-[12px] text-gray-400 mb-4">Share your thoughts, questions, and follow-up replies.</p>

                <form id="comment-form" class="mb-6">
                    <textarea name="comment" placeholder="Share your thoughts on this session..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2.5 text-[14px] text-gray-800 placeholder-gray-400 focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none resize-none" rows="3"></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="bg-gray-900 text-white text-[13px] font-semibold px-4 py-2 rounded-md hover:bg-black transition-colors">
                            Comment
                        </button>
                    </div>
                </form>

                <div class="divide-y divide-gray-200" id="comment-list">
                    @foreach ($comments as $comment)
                        @include('memberpages.partials.course-video-comment', ['comment' => $comment])
                    @endforeach
                </div>

                @if ($comments->isEmpty())
                    <div id="comment-empty-state" class="py-6 text-center">
                        <p class="text-[13px] text-gray-500">No comments yet. Be the first to share your thoughts.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @include('memberpages.partials.course-video-comment-script')

@endsection
