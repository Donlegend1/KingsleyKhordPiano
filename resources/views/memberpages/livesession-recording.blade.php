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
                <span class="text-blue-600 font-medium">{{ $liveshow->title }}</span>
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
            <div id="discussion-section" data-course-id="{{ $liveshow->id }}" data-comment-category="liveshow">
                <h2 class="text-[18px] font-bold text-gray-900 mb-6">Discussion</h2>
                <form id="comment-form" class="mb-8">
                    <textarea name="comment" placeholder="Share your thoughts on this session..."
                        class="w-full bg-gray-50 border border-gray-100 rounded-xl p-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" rows="3"></textarea>
                    <div class="flex justify-end mt-3">
                        <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-blue-700 transition-all">Comment</button>
                    </div>
                </form>

                <div class="space-y-6" id="comment-list">
                    @forelse ($comments as $comment)
                        @include('memberpages.partials.course-video-comment', ['comment' => $comment])
                    @empty
                        <p class="text-sm text-gray-400 text-center py-6">No comments yet. Be the first to share your thoughts.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    @include('memberpages.partials.course-video-comment-script')

@endsection
