@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 max-w-full">
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Personalized Guidance</h2>
        <p class="text-sm text-gray-600">Everyone who submitted a guidance form and/or booked a discovery call, so you can design their roadmap.</p>
    </header>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($entries->isEmpty())
        <div class="bg-white rounded-lg shadow p-10 text-center text-gray-500">
            No submissions or bookings yet.
        </div>
    @else
        <div class="bg-white rounded-xl shadow divide-y divide-gray-100">
            @foreach($entries as $entry)
                <a
                    href="{{ route('admin.personalized-guidance.show', $entry->user) }}"
                    class="flex items-center gap-4 p-4 sm:p-5 hover:bg-gray-50 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>

                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $entry->user->full_name ?? 'Unknown student' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $entry->user->email ?? '' }}</p>
                    </div>

                    <div class="hidden sm:flex items-center gap-1.5 flex-shrink-0">
                        @if($entry->request)
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-50 text-indigo-700">Form</span>
                        @endif
                        @if($entry->bookings->isNotEmpty())
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-50 text-blue-700">Call Booked</span>
                        @endif
                    </div>

                    <span class="hidden sm:block text-xs text-gray-400 flex-shrink-0">{{ $entry->latestAt?->diffForHumans() }}</span>

                    @if($entry->request)
                        @if($entry->request->status === 'reviewed')
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 flex-shrink-0">Reviewed</span>
                        @else
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 flex-shrink-0">Pending</span>
                        @endif
                    @endif
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $entries->links() }}
        </div>
    @endif
</div>
@endsection
