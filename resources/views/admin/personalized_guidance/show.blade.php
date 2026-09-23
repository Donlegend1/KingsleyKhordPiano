@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 max-w-3xl mx-auto">

    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('admin.personalized-guidance.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back to all requests
        </a>

        <a href="{{ route('admin.personalized-guidance.plan.edit', $user) }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-white bg-gray-900 hover:bg-black px-3.5 py-2 rounded-lg transition-colors"
           title="Set personalized roadmap &amp; plan">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Set Personalized Plan
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Student header -->
    <div class="flex items-start justify-between gap-3 bg-white rounded-xl shadow p-5 mb-6">
        <div>
            <p class="text-base font-semibold text-gray-900">{{ $user->full_name ?? 'Unknown student' }}</p>
            <p class="text-sm text-gray-500">{{ $user->email ?? '' }}</p>
        </div>
        @if($request)
            @if($request->status === 'reviewed')
                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 flex-shrink-0">Reviewed</span>
            @else
                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 flex-shrink-0">Pending</span>
            @endif
        @endif
    </div>

    <!-- Guidance form submission -->
    @if($request)
        @php
            $videoId = null;
            $driveFileId = null;
            if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/', $request->youtube_link, $m)) {
                $videoId = $m[1];
            } elseif (preg_match('/drive\.google\.com\/(?:file\/d\/|open\?id=)([A-Za-z0-9_-]{10,})/', $request->youtube_link, $m)) {
                $driveFileId = $m[1];
            }
        @endphp

        <div class="bg-white rounded-xl shadow overflow-hidden mb-6">

            <p class="text-sm font-bold text-gray-900 px-5 pt-5">Guidance Form</p>
            <p class="text-xs text-gray-400 px-5 mb-3">Submitted {{ $request->created_at->diffForHumans() }}</p>

            <div class="aspect-video bg-gray-900">
                @if($videoId)
                    <iframe
                        class="w-full h-full"
                        src="https://www.youtube.com/embed/{{ $videoId }}"
                        title="Submission from {{ $user->full_name ?? 'Student' }}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                @elseif($driveFileId)
                    <div class="relative w-full h-full">
                        <iframe
                            class="w-full h-full"
                            src="https://drive.google.com/file/d/{{ $driveFileId }}/preview"
                            title="Submission from {{ $user->full_name ?? 'Student' }}"
                            frameborder="0"
                            allow="autoplay"
                            allowfullscreen
                        ></iframe>
                        {{-- Covers Google Drive's built-in "open in new window" icon, which can't be removed from the cross-origin preview UI --}}
                        <div class="absolute top-0 right-0 w-16 h-16"></div>
                    </div>
                @else
                    <a href="{{ $request->youtube_link }}" target="_blank" rel="noopener noreferrer" class="w-full h-full flex items-center justify-center text-gray-300 text-sm hover:text-white">
                        Couldn't preview this link — open it directly
                    </a>
                @endif
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 mb-5 text-sm">
                    @if($request->archetype)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">Archetype</span>
                            <span class="text-gray-700">{{ $request->archetype }}</span>
                        </div>
                    @endif
                    @if($request->experience_level)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">Experience</span>
                            <span class="text-gray-700">{{ $request->experience_level }}</span>
                        </div>
                    @endif
                    @if($request->chord_vocabulary)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">Chord Vocabulary</span>
                            <span class="text-gray-700">{{ $request->chord_vocabulary }}</span>
                        </div>
                    @endif
                    @if($request->playing_by_ear)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">Playing by Ear</span>
                            <span class="text-gray-700">{{ $request->playing_by_ear }}</span>
                        </div>
                    @endif
                    @if($request->key_fluency)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">All 12 Keys?</span>
                            <span class="text-gray-700">{{ $request->key_fluency }}</span>
                        </div>
                    @endif
                    @if($request->inspiration_pianist)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">Wants to Play Like</span>
                            <span class="text-gray-700">{{ $request->inspiration_pianist }}</span>
                        </div>
                    @endif
                    @if($request->practice_days_per_week)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">Days / Week</span>
                            <span class="text-gray-700">{{ $request->practice_days_per_week }}</span>
                        </div>
                    @endif
                    @if($request->practice_time_per_day)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">Time / Day</span>
                            <span class="text-gray-700">{{ $request->practice_time_per_day }}</span>
                        </div>
                    @endif
                    @if($request->style_focus)
                        <div>
                            <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px]">Style Focus</span>
                            <span class="text-gray-700">{{ $request->style_focus }}</span>
                        </div>
                    @endif
                </div>

                @if($request->primary_goal)
                    <div class="mb-5">
                        <span class="block text-gray-400 font-semibold uppercase tracking-wide text-[10px] mb-1">Primary Goal</span>
                        <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $request->primary_goal }}</p>
                    </div>
                @elseif($request->details)
                    <p class="text-sm text-gray-600 leading-relaxed mb-5 whitespace-pre-line">{{ $request->details }}</p>
                @else
                    <p class="text-sm text-gray-400 italic mb-5">No additional notes provided.</p>
                @endif

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ $request->youtube_link }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-indigo-600 hover:underline">
                        {{ $driveFileId ? 'Open in Google Drive' : 'Open on YouTube' }}
                    </a>
                    @if($request->status !== 'reviewed')
                        <form action="{{ route('admin.personalized-guidance.reviewed', $request) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-gray-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-900">
                                Mark Reviewed
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    @else
        <div class="bg-white rounded-xl shadow p-5 mb-6 text-sm text-gray-400 italic">
            No guidance form submitted yet.
        </div>
    @endif

    <!-- Discovery call bookings -->
    <div class="bg-white rounded-xl shadow p-5">
        <p class="text-sm font-bold text-gray-900 mb-3">Discovery Call {{ $bookings->count() === 1 ? 'Booking' : 'Bookings' }}</p>

        @if($bookings->isEmpty())
            <p class="text-sm text-gray-400 italic">No call booked.</p>
        @else
            <div class="flex flex-col divide-y divide-gray-100">
                @foreach($bookings as $booking)
                    @php
                        $bookingDateTime = \Carbon\Carbon::parse($booking->date . ' ' . $booking->time, 'Africa/Lagos');
                    @endphp
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $bookingDateTime->format('D, M j, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $bookingDateTime->format('H:i') }} (West Africa Time)</p>
                        </div>
                        @if($booking->zoom_join_url)
                            <a href="{{ $booking->zoom_join_url }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-indigo-600 hover:underline flex-shrink-0">
                                Join via Zoom
                            </a>
                        @else
                            <span class="text-xs text-gray-400 flex-shrink-0">No Zoom link yet</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
