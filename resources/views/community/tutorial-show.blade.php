@extends('layouts.hub')

@section('title', $tutorial->title)

@section('breadcrumbs')
    @include('community.partials.breadcrumbs', ['items' => [
        ['label' => 'Tutorials', 'url' => route('community.subcategory', 'lessons')],
        ['label' => $tutorial->title],
    ]])
@endsection

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-12">

    {{-- Video --}}
    <div class="relative aspect-video w-full bg-black rounded-2xl overflow-hidden shadow-sm">
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
            {{-- Blocks Google Drive's built-in "open in new window" icon, which can't be removed from the cross-origin preview UI --}}
            <div class="absolute top-0 right-0 w-16 h-16"></div>
        @else
            <video src="{{ $tutorial->video_url }}" class="w-full h-full" controls></video>
        @endif
    </div>

    <div class="mt-5 mb-10">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $tutorial->title }}</h2>
        @if($tutorial->description)
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 leading-relaxed">{{ $tutorial->description }}</p>
        @endif
    </div>

    {{-- Comments --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-white/10 rounded-2xl p-6">
        <div class="flex items-center gap-2.5 mb-5">
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-400">
                <i class="fa-regular fa-comments text-sm"></i>
            </span>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                Comments <span class="text-gray-400 font-medium">({{ $comments->count() }})</span>
            </h3>
        </div>

        <form action="{{ route('community.tutorials.comment') }}" method="POST" class="mb-8">
            @csrf
            <input type="hidden" name="tutorial_id" value="{{ $tutorial->id }}">
            <textarea
                name="comment"
                rows="3"
                placeholder="Share your thoughts on this tutorial..."
                class="w-full border border-gray-200 dark:border-white/10 dark:bg-black/20 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-[#C85A5A] focus:border-[#C85A5A] outline-none transition resize-none"
                required
            ></textarea>
            <div class="flex justify-end mt-3">
                <button type="submit" class="flex items-center gap-2 bg-[#C85A5A] hover:bg-[#B54B4B] text-white text-sm font-semibold px-5 py-2.5 rounded-full transition-colors">
                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                    Post
                </button>
            </div>
        </form>

        <div class="divide-y divide-gray-100 dark:divide-white/10">
            @forelse($comments as $comment)
                <div class="flex gap-3 py-4 first:pt-0" x-data="{ editing: false }">
                    <div class="w-9 h-9 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center text-gray-500 dark:text-gray-300 font-semibold text-xs flex-shrink-0">
                        {{ substr($comment->user->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $comment->user->name ?? 'Member' }}</p>
                            <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            @if($comment->user_id === auth()->id())
                                <div class="flex items-center gap-3 ml-auto">
                                    <button type="button" @click="editing = !editing" class="text-xs font-medium text-gray-500 hover:text-gray-900 dark:hover:text-white">
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

                        <p x-show="!editing" class="text-sm text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">{{ $comment->comment }}</p>

                        @if($comment->user_id === auth()->id())
                            <form x-show="editing" x-cloak action="{{ route('community.tutorials.comment.update', $comment->id) }}" method="POST" class="mt-2">
                                @csrf
                                @method('PUT')
                                <textarea
                                    name="comment"
                                    rows="2"
                                    class="w-full border border-gray-200 dark:border-white/10 dark:bg-black/20 rounded-lg p-2 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-[#C85A5A] focus:border-[#C85A5A] outline-none transition resize-none"
                                    required
                                >{{ $comment->comment }}</textarea>
                                <div class="flex justify-end mt-1.5">
                                    <button type="submit" class="bg-[#C85A5A] hover:bg-[#B54B4B] text-white text-xs font-semibold px-3.5 py-1.5 rounded-full transition-colors">
                                        Save
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center">
                    <p class="text-sm text-gray-400">No comments yet. Be the first to share your thoughts.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@if($tutorial->video_type === 'iframe')
<script>
    function initVideoScripts() {
        const container = document.querySelector('.aspect-video');
        if (container) {
            const scripts = container.querySelectorAll("script");
            scripts.forEach(oldScript => {
                const src = oldScript.getAttribute("src");
                if (src) {
                    const existingScript = document.querySelector(`script[src="${src}"]`);
                    if (!existingScript) {
                        const newScript = document.createElement("script");
                        newScript.src = src;
                        newScript.async = true;
                        const type = oldScript.getAttribute("type");
                        if (type) {
                            newScript.type = type;
                        }
                        document.head.appendChild(newScript);
                    }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener("DOMContentLoaded", initVideoScripts);
    } else {
        initVideoScripts();
    }
</script>
@endif

@endsection
