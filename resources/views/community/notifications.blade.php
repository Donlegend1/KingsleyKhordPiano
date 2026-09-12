@extends("layouts.hub")

@section("title", "Notifications")

@section("breadcrumbs")
    @include('community.partials.breadcrumbs', ['items' => [['label' => 'Notifications']]])
@endsection

@section("content")
<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    Mark all read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        @forelse($notifications as $notification)
            @php
                $data      = $notification->data;
                $firstName = $data['data']['user'] ?? 'Someone';
                $type      = $data['data']['type'] ?? '';
                $avatar    = $data['data']['by_user_avatar'] ?? null;
                $isUnread  = is_null($notification->read_at);
                $initials  = strtoupper(substr($firstName, 0, 1));
                $notifUrl  = $data['data']['url'] ?? null;
            @endphp

            <a href="{{ $notifUrl }}"
                class="flex items-start gap-3 px-5 py-4 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40 {{ !$loop->first ? 'border-t border-gray-100 dark:border-gray-700' : '' }} {{ $isUnread ? 'bg-blue-50/60 dark:bg-blue-900/10' : '' }}">

                <div class="flex-shrink-0 mt-0.5">
                    @if(!empty($avatar))
                        <img src="{{ $avatar }}" alt="{{ $firstName }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                    @else
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-sm font-bold text-indigo-600 dark:text-indigo-400 ring-2 ring-white dark:ring-gray-800">
                            {{ $initials }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 dark:text-gray-200 leading-snug">
                        <span class="font-semibold">{{ $firstName }}</span>
                        @if($type === 'comment') commented on your post
                        @elseif($type === 'reply') replied to your comment
                        @elseif($type === 'like') liked your post
                        @else interacted with your post
                        @endif
                    </p>
                    <span class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 block">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </div>

                @if($isUnread)
                    <span class="flex-shrink-0 mt-2 w-2 h-2 rounded-full bg-blue-500"></span>
                @endif
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No notifications yet</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">You'll be notified when something happens in the community</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links('pagination.community') }}
        </div>
    @endif
</div>
@endsection
