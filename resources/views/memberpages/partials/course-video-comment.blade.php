<div class="flex gap-4 p-4 rounded-xl hover:bg-gray-50 transition-colors" data-comment-id="{{ $comment->id }}">
    <div class="w-10 h-10 bg-[#2563EB]/10 rounded-full flex items-center justify-center flex-shrink-0">
        <span class="text-[#2563EB] font-bold text-sm">
            {{ $comment->user ? substr($comment->user->first_name ?? $comment->user->name ?? 'U', 0, 1) : 'U' }}
        </span>
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2 mb-1">
            <div class="flex items-center gap-2">
                <p class="text-[14px] font-bold text-gray-900">
                    {{ $comment->user ? ($comment->user->first_name . ' ' . $comment->user->last_name) : 'User' }}
                </p>
                <span class="text-[11px] text-gray-400">• {{ $comment->created_at->diffForHumans() }}</span>
            </div>

            @if ($comment->user_id === auth()->id())
                <div class="relative comment-menu">
                    <button type="button" class="comment-menu-toggle text-gray-400 hover:text-gray-600 px-1">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="comment-menu-dropdown hidden absolute right-0 mt-1 w-32 bg-white border border-gray-100 rounded-lg shadow-md z-10">
                        <button type="button" class="comment-edit-btn block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Edit</button>
                        <button type="button" class="comment-delete-btn block w-full px-4 py-2 text-left text-sm text-red-500 hover:bg-gray-50">Delete</button>
                    </div>
                </div>
            @endif
        </div>

        <p class="text-[13px] text-gray-600 leading-relaxed comment-text">{{ $comment->comment }}</p>

        @if ($comment->user_id === auth()->id())
            <div class="comment-edit-form hidden mt-2 space-y-2">
                <textarea class="comment-edit-input w-full bg-gray-50 border border-gray-100 rounded-xl p-3 text-[13px] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent transition-all outline-none" rows="2">{{ $comment->comment }}</textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" class="comment-edit-cancel text-[12px] font-semibold text-gray-500 px-3 py-1.5 rounded-lg hover:bg-gray-100">Cancel</button>
                    <button type="button" class="comment-edit-save text-[12px] font-semibold text-white bg-[#2563EB] px-3 py-1.5 rounded-lg hover:bg-[#1D4ED8]">Save</button>
                </div>
            </div>
        @endif
    </div>
</div>
