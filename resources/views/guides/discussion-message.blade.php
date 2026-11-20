@if($message->body === null)
    <p class="text-sm italic text-gray-500">Comment removed.</p>
@else
    @php
        $name = trim($message->author?->getRawOriginal('display_name') ?? '') ?: ($message->author?->first_name ?: 'Former member');
    @endphp
    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
        <p class="break-words text-sm font-bold text-[#151a22]">{{ $name }}</p>
        <time datetime="{{ $message->created_at->toIso8601String() }}" class="text-xs text-gray-600">{{ $message->created_at->format('M j, Y') }}</time>
        @if($canModerate || ($viewer && (int) $message->user_id === (int) $viewer->id))
            <form method="POST" action="{{ route('guides.discussion.destroy', ['slug' => $guideSlug, 'comment' => $message->id, 'comments_page' => $discussion->currentPage()]) }}" class="ml-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-semibold text-red-700 underline-offset-4 hover:underline">Remove</button>
            </form>
        @endif
    </div>
    <p class="mt-2 whitespace-pre-wrap break-words text-sm leading-6 text-[#373f50] [overflow-wrap:anywhere]">{{ $message->body }}</p>
@endif
