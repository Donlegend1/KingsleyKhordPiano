@php
    $viewer = auth()->user();
    $canModerate = in_array($viewer?->role, [\App\Enums\Roles\UserRoles::ADMIN->value, \App\Enums\Roles\UserRoles::SUPERADMIN->value], true);
@endphp

<section id="guide-discussion" aria-labelledby="guide-discussion-heading" class="scroll-mt-5 border-t border-black/10 bg-white">
    <div class="mx-auto max-w-4xl px-5 py-10 sm:px-8 sm:py-14">
        <h2 id="guide-discussion-heading" class="text-2xl font-bold tracking-tight text-[#151a22] sm:text-3xl">Questions &amp; Discussion</h2>
        <p class="mt-2 text-sm leading-6 text-[#4f5667]">What did you notice when you tried this at the piano? Share what worked or ask about a part you’re still figuring out.</p>

        @if(session('guideDiscussionStatus'))
            <p role="status" class="mt-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('guideDiscussionStatus') }}</p>
        @endif
        @if($errors->guideDiscussion->any())
            <p role="alert" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ $errors->guideDiscussion->first() }}</p>
        @endif

        @if($viewer)
            <form method="POST" action="{{ route('guides.discussion.store', $guideSlug) }}" class="mt-5 rounded-xl border border-gray-200 bg-[#f9f7f0] p-4">
                @csrf
                <label for="guide-comment" class="block text-sm font-semibold">Write a comment</label>
                <textarea id="guide-comment" name="body" rows="3" maxlength="2000" required
                          class="mt-2 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm leading-6 focus:outline focus:outline-2 focus:outline-[#1447A6]">{{ !old('parent_id') ? old('body') : '' }}</textarea>
                <button type="submit" class="mt-3 rounded-lg bg-[#1447A6] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#0F3A8C]">Post Comment</button>
            </form>
        @else
            <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 rounded-xl border border-gray-200 bg-[#f9f7f0] px-4 py-3">
                <p class="mr-auto text-sm text-[#4f5667]">Sign in with a free account to comment or reply.</p>
                <a href="{{ route('guides.discussion.join', $guideSlug) }}" class="text-sm font-bold text-[#1447A6] underline-offset-4 hover:underline">Sign in</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-[#1447A6] px-3 py-2 text-sm font-bold text-white hover:bg-[#0F3A8C]">Create a free account</a>
            </div>
        @endif

        <div class="mt-6 space-y-3">
            @forelse($discussion as $comment)
                <article class="rounded-xl border border-gray-200 p-4 sm:p-5">
                    @include('guides.discussion-message', ['message' => $comment])

                    @if($comment->replies->isNotEmpty())
                        <div class="mt-4 space-y-3 border-l-2 border-gray-200 pl-3 sm:pl-4">
                            @foreach($comment->replies as $reply)
                                <div class="rounded-lg bg-gray-50 px-3 py-2.5">
                                    @include('guides.discussion-message', ['message' => $reply])
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($viewer && $comment->body !== null)
                        <details class="mt-3" @if((int) old('parent_id') === $comment->id) open @endif>
                            <summary class="w-fit cursor-pointer text-sm font-bold text-[#1447A6]">Reply</summary>
                            <form method="POST" action="{{ route('guides.discussion.store', ['slug' => $guideSlug, 'comments_page' => $discussion->currentPage()]) }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <label for="reply-{{ $comment->id }}" class="sr-only">Write a reply</label>
                                <textarea id="reply-{{ $comment->id }}" name="body" rows="2" maxlength="2000" required
                                          class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline focus:outline-2 focus:outline-[#1447A6]">{{ (int) old('parent_id') === $comment->id ? old('body') : '' }}</textarea>
                                <button type="submit" class="mt-2 rounded-lg bg-[#1447A6] px-3 py-2 text-sm font-bold text-white hover:bg-[#0F3A8C]">Post Reply</button>
                            </form>
                        </details>
                    @endif
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-gray-200 px-4 py-5 text-sm text-gray-600">No comments yet. Be the first to ask a question or share what you heard.</p>
            @endforelse
        </div>

        @if($discussion->hasPages())
            <div class="mt-5">{{ $discussion->links('pagination.community') }}</div>
        @endif
    </div>
</section>
