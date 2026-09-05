@extends('layouts.admin')

@section('content')
<style>[x-cloak] { display: none !important; }</style>
<div class="p-4 sm:p-6 max-w-full" x-data="pianoCoachingAdd()">
    <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Piano Coaching Members</h2>
            <p class="text-sm text-gray-600">Members who can book a one-on-one piano coaching session</p>
        </div>
        <div class="text-sm text-gray-500">
            {{ $members->total() }} {{ \Illuminate\Support\Str::plural('member', $members->total()) }}
        </div>
    </header>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Add a subscribed member</h3>
        <p class="text-sm text-gray-500 mb-4">Search by name or email, then grant piano coaching access.</p>

        <div class="relative max-w-xl">
            <input
                type="text"
                x-model="query"
                @input.debounce.300ms="search()"
                placeholder="Search subscribed members..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
            >
            <div x-show="loading" class="absolute right-3 top-2.5 text-gray-400 text-sm" x-cloak>Searching...</div>

            <div
                x-show="results.length > 0"
                x-cloak
                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-72 overflow-y-auto"
            >
                <template x-for="user in results" :key="user.id">
                    <form method="POST" action="{{ route('admin.piano-coaching.store') }}" class="flex items-center justify-between gap-3 px-4 py-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50">
                        @csrf
                        <input type="hidden" name="user_id" :value="user.id">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate" x-text="user.name"></div>
                            <div class="text-xs text-gray-500 truncate" x-text="user.email + (user.plan ? ' · ' + user.plan : '')"></div>
                        </div>
                        <button type="submit" class="shrink-0 bg-blue-600 text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-blue-700">
                            Add
                        </button>
                    </form>
                </template>
            </div>

            <p x-show="query.length >= 2 && !loading && results.length === 0" class="mt-2 text-sm text-gray-500" x-cloak>
                No subscribed members found who are not already on this list.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.piano-coaching.index') }}" class="flex gap-2 max-w-xl">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search coaching members..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
                >
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900">Search</button>
                @if($search !== '')
                    <a href="{{ route('admin.piano-coaching.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline">Clear</a>
                @endif
            </form>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions booked</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($members as $member)
                    @php
                        $tier = strtolower((string) (data_get($member->metadata, 'tier') ?? ($member->premium ? 'premium' : 'standard')));
                        $duration = strtolower((string) (data_get($member->metadata, 'duration') ?? $member->subscription_type ?? ''));
                        $planLabel = (str_contains($tier, 'premium') ? 'Premium' : 'Standard') . (match ($duration) {
                            'month', 'monthly' => ' · Monthly',
                            'quarter', 'quarterly' => ' · Quarterly',
                            'year', 'yearly' => ' · Yearly',
                            default => '',
                        });
                        $canBookNow = $member->canAccessPianoCoaching();
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $member->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $member->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $planLabel }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($canBookNow)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Can book</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Entitled, inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $member->live_coaching_bookings_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <form method="POST" action="{{ route('admin.piano-coaching.destroy', $member) }}" onsubmit="return confirm('Remove piano coaching access for {{ $member->full_name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                            No piano coaching members yet. Add a subscribed member above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">
            {{ $members->links() }}
        </div>
    </div>
</div>

<script>
    function pianoCoachingAdd() {
        return {
            query: '',
            results: [],
            loading: false,
            async search() {
                if (this.query.trim().length < 2) {
                    this.results = [];
                    return;
                }
                this.loading = true;
                try {
                    const url = @json(route('admin.piano-coaching.eligible')) + '?q=' + encodeURIComponent(this.query.trim());
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    this.results = await response.json();
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
@endsection
