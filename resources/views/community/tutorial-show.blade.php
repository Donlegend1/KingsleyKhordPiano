@extends('layouts.community')

@section('content')

<!-- Header Section -->
<div class="bg-white dark:bg-[#161617] border-b border-gray-200 dark:border-white/10 mb-6">
    <div class="px-6 py-4 flex items-center gap-2 text-sm font-medium">
        <a href="{{ route('community.index') }}" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition">Overview</a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('community.subcategory', 'lessons') }}" class="text-gray-900 dark:text-white hover:text-gray-700 dark:hover:text-gray-200 transition">Tutorial</a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="flex flex-col lg:flex-row gap-8 items-start">

        {{-- Left: Video + details --}}
        <div class="flex-1 min-w-0 w-full">
            <div class="aspect-video w-full bg-black rounded-2xl overflow-hidden shadow-sm">
                @if($tutorial->video_type === 'vimeo')
                    <iframe
                        src="https://player.vimeo.com/video/{{ $tutorial->video_url }}?autoplay=0"
                        class="w-full h-full border-0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                @elseif($tutorial->video_type === 'youtube')
                    <iframe
                        src="https://www.youtube.com/embed/{{ $tutorial->video_url }}"
                        class="w-full h-full border-0"
                        allow="autoplay; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                @elseif($tutorial->video_type === 'google')
                    <iframe
                        src="https://drive.google.com/file/d/{{ $tutorial->video_url }}/preview"
                        class="w-full h-full border-0"
                        allow="autoplay"
                        allowfullscreen
                    ></iframe>
                @else
                    <video src="{{ $tutorial->video_url }}" class="w-full h-full" controls></video>
                @endif
            </div>

            <div class="mt-5">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $tutorial->title }}</h2>
                @if($tutorial->description)
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 leading-relaxed">{{ $tutorial->description }}</p>
                @endif
            </div>
        </div>

        {{-- Right: Comments sidebar --}}
        <aside class="w-full lg:w-[380px] flex-shrink-0">
            <div class="bg-white dark:bg-[#161617] border border-gray-200 dark:border-white/10 rounded-2xl p-5 lg:sticky lg:top-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">
                    Comments <span class="text-gray-400 font-medium">({{ $comments->count() }})</span>
                </h3>

                <form action="{{ route('community.tutorials.comment') }}" method="POST" class="mb-6">
                    @csrf
                    <input type="hidden" name="tutorial_id" value="{{ $tutorial->id }}">
                    <textarea
                        name="comment"
                        rows="3"
                        placeholder="Share your thoughts on this tutorial..."
                        class="w-full bg-gray-50 dark:bg-black border border-gray-200 dark:border-white/10 rounded-xl p-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        required
                    ></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition">
                            Post
                        </button>
                    </div>
                </form>

                <div class="space-y-5 max-h-[60vh] overflow-y-auto pr-1">
                    @forelse($comments as $comment)
                        <div class="flex gap-3" x-data="{ editing: false }">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs flex-shrink-0">
                                {{ substr($comment->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $comment->user->name ?? 'Member' }}</p>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($comment->user_id === auth()->id())
                                        <div class="flex items-center gap-3 ml-auto">
                                            <button type="button" @click="editing = !editing" class="text-xs font-medium text-indigo-600 hover:underline">
                                                <span x-text="editing ? 'Cancel' : 'Edit'"></span>
                                            </button>
                                            <form action="{{ route('community.tutorials.comment.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Delete this comment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-medium text-red-500 hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <p x-show="!editing" class="text-sm text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">{{ $comment->comment }}</p>

                                @if($comment->user_id === auth()->id())
                                    <form x-show="editing" x-cloak action="{{ route('community.tutorials.comment.update', $comment->id) }}" method="POST" class="mt-1">
                                        @csrf
                                        @method('PUT')
                                        <textarea
                                            name="comment"
                                            rows="2"
                                            class="w-full bg-gray-50 dark:bg-black border border-gray-200 dark:border-white/10 rounded-lg p-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                                            required
                                        >{{ $comment->comment }}</textarea>
                                        <div class="flex justify-end mt-1.5">
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-6">No comments yet. Be the first to share your thoughts.</p>
                    @endforelse
                </div>
            </div>
        </aside>

    </div>
</div>

@endsection
