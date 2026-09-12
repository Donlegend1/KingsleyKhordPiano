@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 max-w-full">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Personalized Guidance Requests</h2>
        <p class="text-sm text-gray-600">Review submitted playing videos and notes to design each student's roadmap.</p>
    </header>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($requests->isEmpty())
        <div class="bg-white rounded-lg shadow p-10 text-center text-gray-500">
            No submissions yet.
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($requests as $request)
                @php
                    $videoId = null;
                    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/', $request->youtube_link, $m)) {
                        $videoId = $m[1];
                    }
                @endphp
                <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col">

                    <div class="aspect-video bg-gray-900">
                        @if($videoId)
                            <iframe
                                class="w-full h-full"
                                src="https://www.youtube.com/embed/{{ $videoId }}"
                                title="Submission from {{ $request->user->full_name ?? 'Student' }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        @else
                            <a href="{{ $request->youtube_link }}" target="_blank" rel="noopener noreferrer" class="w-full h-full flex items-center justify-center text-gray-300 text-sm hover:text-white">
                                Couldn't preview this link — open on YouTube
                            </a>
                        @endif
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $request->user->full_name ?? 'Unknown student' }}</p>
                                <p class="text-xs text-gray-500">{{ $request->user->email ?? '' }}</p>
                            </div>
                            @if($request->status === 'reviewed')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 flex-shrink-0">Reviewed</span>
                            @else
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 flex-shrink-0">Pending</span>
                            @endif
                        </div>

                        @if($request->details)
                            <p class="text-sm text-gray-600 leading-relaxed mb-4 whitespace-pre-line">{{ $request->details }}</p>
                        @else
                            <p class="text-sm text-gray-400 italic mb-4">No additional notes provided.</p>
                        @endif

                        <div class="mt-auto flex items-center justify-between gap-3 pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-400">Submitted {{ $request->created_at->diffForHumans() }}</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ $request->youtube_link }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-indigo-600 hover:underline">
                                    Open on YouTube
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
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
