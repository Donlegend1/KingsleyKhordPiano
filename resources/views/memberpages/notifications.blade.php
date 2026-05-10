@extends('layouts.member')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Your activity and updates from KingsleyKhord</p>
        </div>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    {{-- Notifications List --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

        @forelse($notifications as $notification)
            @php
                $nd        = $notification->data['data'] ?? $notification->data;
                $url       = $nd['url'] ?? '#';
                $message   = $nd['title'] ?? $notification->data['message'] ?? 'You have a new notification';
                $section   = $nd['section'] ?? null;
                $type      = $nd['type'] ?? null;
                $avatar    = $nd['by_user_avatar'] ?? null;
                $firstName = $nd['user'] ?? null;
                $initials  = $firstName ? strtoupper(substr($firstName, 0, 1)) : '?';
                $isUnread  = is_null($notification->read_at);
            @endphp

            <div class="relative flex items-start gap-4 px-5 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40 {{ $isUnread ? 'bg-blue-50/60 dark:bg-blue-900/10' : '' }}">

                {{-- Unread indicator bar --}}
                @if($isUnread)
                    <span class="absolute left-0 inset-y-0 w-[3px] bg-indigo-500 rounded-r-full"></span>
                @endif

                {{-- Avatar --}}
                <div class="flex-shrink-0 mt-0.5">
                    @if($avatar)
                        <img src="{{ $avatar }}" alt="{{ $firstName }}"
                             class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800">
                    @else
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-sm font-bold text-indigo-600 dark:text-indigo-400 ring-2 ring-white dark:ring-gray-800">
                            {{ $initials }}
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        @if($section)
                            <span class="text-[10px] font-bold uppercase tracking-wider text-pink-500 dark:text-pink-400 bg-pink-50 dark:bg-pink-900/30 px-1.5 py-0.5 rounded">
                                {{ $section }}
                            </span>
                        @endif
                        @if($isUnread)
                            <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0 ml-auto"></span>
                        @endif
                    </div>

                    @if($type)
                        <p class="text-sm text-gray-800 dark:text-gray-200 leading-snug">
                            @if($firstName)
                                <span class="font-semibold">{{ $firstName }}</span>
                            @endif
                            @if($type === 'comment') commented on your post
                            @elseif($type === 'reply') replied to your comment
                            @elseif($type === 'like') liked your post
                            @else {{ $message }}
                            @endif
                        </p>
                    @else
                        <p class="text-sm text-gray-800 dark:text-gray-200 leading-snug">{{ $message }}</p>
                    @endif

                    <div class="flex items-center gap-4 mt-1.5">
                        <span class="text-xs text-gray-400 dark:text-gray-500">
                            <i class="fa fa-clock mr-0.5"></i>
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                        @if($url && $url !== '#')
                            <a href="{{ $url }}"
                               class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline transition-colors">
                                View →
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Mark as read (per-item) --}}
                @if($isUnread)
                    <form method="POST" action="{{ route('notifications.markRead', $notification->id) }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors"
                            title="Mark as read">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </form>
                @endif
            </div>

        @empty
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">All caught up!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">You have no notifications yet. We'll let you know when something happens.</p>
                <a href="/home" class="mt-5 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Dashboard
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $notifications->links() }}
        </div>
    @endif

    {{-- Back link --}}
    <div class="mt-4 text-center">
        <a href="javascript:history.back()" class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            ← Go back
        </a>
    </div>

</div>
@endsection
