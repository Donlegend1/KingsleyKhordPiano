@extends("layouts.hub")

@section("title", "Notifications")

@section("breadcrumbs")
    @include('community.partials.breadcrumbs', ['items' => [['label' => 'Notifications']]])
@endsection

@section("content")
<div class="p-6" x-data="{
        openSettings: false,
        notifPref: '{{ auth()->user()->notification_preference ?? 'email' }}',
        saving: false,
        pushError: '',
        async savePreference(value) {
            const previous = this.notifPref;
            this.notifPref = value;
            try {
                const res = await fetch('{{ route('notifications.updatePreference') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ notification_preference: value }),
                });
                if (!res.ok) { this.notifPref = previous; }
            } catch (e) {
                this.notifPref = previous;
            }
        },
        async setPref(value) {
            if (this.saving) return;
            this.pushError = '';
            if (value !== 'push') {
                this.saving = true;
                await this.savePreference(value);
                this.saving = false;
                return;
            }

            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                this.pushError = 'Push notifications are not supported in this browser.';
                this.notifPref = '{{ auth()->user()->notification_preference ?? 'email' }}';
                return;
            }

            this.saving = true;
            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    this.pushError = 'Notification permission was denied.';
                    this.notifPref = '{{ auth()->user()->notification_preference ?? 'email' }}';
                    return;
                }

                const registration = await navigator.serviceWorker.register('/serviceworker.js');
                await navigator.serviceWorker.ready;

                const urlBase64ToUint8Array = (base64String) => {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const rawData = atob(base64);
                    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
                };

                let subscription = await registration.pushManager.getSubscription();
                if (!subscription) {
                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array('{{ config('webpush.vapid.public_key') }}'),
                    });
                }

                await fetch('{{ route('webpush.subscribe') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify(subscription.toJSON()),
                });

                await this.savePreference('push');
            } catch (e) {
                this.pushError = 'Could not enable push notifications.';
                this.notifPref = '{{ auth()->user()->notification_preference ?? 'email' }}';
            } finally {
                this.saving = false;
            }
        }
     }">
    <div class="flex flex-wrap items-center justify-between gap-y-2 gap-x-3 mb-4">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
        <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
            @if($notifications->total() > 0)
                <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap">
                        Mark all read
                    </button>
                </form>
            @endif
            <div class="relative" @click.outside="openSettings = false">
                <button type="button" @click="openSettings = !openSettings" aria-label="Notification settings"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>

                <div x-show="openSettings" x-cloak x-transition
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-lg p-3 z-50 text-left">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2 px-1">Notify me via</p>
                    <label class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                        <input type="radio" name="notifPrefFull" value="push" x-model="notifPref" :disabled="saving" @change="setPref('push')" class="text-indigo-600 focus:ring-indigo-400">
                        <span class="text-sm text-gray-700 dark:text-gray-200">Push notifications</span>
                    </label>
                    <label class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                        <input type="radio" name="notifPrefFull" value="email" x-model="notifPref" :disabled="saving" @change="setPref('email')" class="text-indigo-600 focus:ring-indigo-400">
                        <span class="text-sm text-gray-700 dark:text-gray-200">Email notifications</span>
                    </label>
                    <label class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/60 cursor-pointer">
                        <input type="radio" name="notifPrefFull" value="disabled" x-model="notifPref" :disabled="saving" @change="setPref('disabled')" class="text-indigo-600 focus:ring-indigo-400">
                        <span class="text-sm text-gray-700 dark:text-gray-200">Disabled</span>
                    </label>
                    <p x-show="pushError" x-cloak x-text="pushError" class="text-xs text-red-500 mt-1.5 px-1"></p>
                </div>
            </div>
        </div>
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
