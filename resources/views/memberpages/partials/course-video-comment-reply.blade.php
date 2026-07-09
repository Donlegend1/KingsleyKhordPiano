<div class="flex gap-3 py-3 first:pt-0" data-reply-id="{{ $reply->id }}">
    <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
        <span class="text-gray-500 font-semibold text-[11px]">
            {{ $reply->user ? substr($reply->user->first_name ?? $reply->user->name ?? 'U', 0, 1) : 'U' }}
        </span>
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-baseline gap-2">
            <p class="text-[13px] font-semibold text-gray-900">
                {{ $reply->user ? ($reply->user->first_name . ' ' . $reply->user->last_name) : 'User' }}
            </p>
            <span class="text-[11px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
        </div>
        <p class="text-[13px] text-gray-600 leading-relaxed mt-0.5">{{ $reply->reply }}</p>
    </div>
</div>
