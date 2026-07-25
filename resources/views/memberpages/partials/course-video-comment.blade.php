<div class="flex gap-3.5 py-5 first:pt-0" data-comment-id="{{ $comment->id }}">
    <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
        <span class="text-gray-500 font-semibold text-xs">
            {{ $comment->user ? substr($comment->user->first_name ?? $comment->user->name ?? 'U', 0, 1) : 'U' }}
        </span>
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <div class="flex items-baseline gap-2">
                <p class="text-[13.5px] font-semibold text-gray-900">
                    {{ $comment->user ? ($comment->user->first_name . ' ' . $comment->user->last_name) : 'User' }}
                </p>
                <span class="text-[11.5px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
            </div>

            @if ($comment->user_id === auth()->id())
                <div class="relative comment-menu">
                    <button type="button" class="comment-menu-toggle text-gray-300 hover:text-gray-500 px-1 transition-colors">
                        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                    </button>
                    <div class="comment-menu-dropdown hidden absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-xl shadow-lg z-10 overflow-hidden">
                        <button type="button" class="comment-edit-btn block w-full px-4 py-2 text-left text-[13px] text-gray-600 hover:bg-gray-50">Edit</button>
                        <button type="button" class="comment-delete-btn block w-full px-4 py-2 text-left text-[13px] text-red-500 hover:bg-gray-50">Delete</button>
                    </div>
                </div>
            @endif
        </div>

        <p class="text-[13.5px] text-gray-600 leading-relaxed mt-1 comment-text">{{ $comment->comment }}</p>

        @if ($comment->user_id === auth()->id())
            <div class="comment-edit-form hidden mt-3 space-y-2">
                <textarea class="comment-edit-input w-full bg-white border border-gray-200 rounded-2xl p-3 text-[13.5px] focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none resize-none" rows="2">{{ $comment->comment }}</textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" class="comment-edit-cancel text-[12px] font-semibold text-gray-500 px-3.5 py-1.5 rounded-full hover:bg-gray-100 transition-colors">Cancel</button>
                    <button type="button" class="comment-edit-save text-[12px] font-semibold text-white bg-gray-900 px-3.5 py-1.5 rounded-full hover:bg-black transition-colors">Save</button>
                </div>
            </div>
        @endif

        <button type="button" class="comment-reply-toggle mt-2 text-[12px] font-semibold text-gray-500 hover:text-gray-900 transition-colors">
            Reply
        </button>

        <div class="comment-reply-form hidden mt-3 space-y-2">
            <textarea class="comment-reply-input w-full bg-white border border-gray-200 rounded-2xl p-3 text-[13px] focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors outline-none resize-none" rows="2" placeholder="Write a reply..."></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" class="comment-reply-cancel text-[12px] font-semibold text-gray-500 px-3.5 py-1.5 rounded-full hover:bg-gray-100 transition-colors">Cancel</button>
                <button type="button" class="comment-reply-submit text-[12px] font-semibold text-white bg-gray-900 px-3.5 py-1.5 rounded-full hover:bg-black transition-colors">Reply</button>
            </div>
        </div>

        <div class="comment-replies-list mt-4 space-y-4 border-gray-100 {{ $comment->replies->count() ? 'border-l-2 pl-4' : '' }}">
            @foreach ($comment->replies as $reply)
                @include('memberpages.partials.course-video-comment-reply', ['reply' => $reply])
            @endforeach
        </div>
    </div>
</div>
