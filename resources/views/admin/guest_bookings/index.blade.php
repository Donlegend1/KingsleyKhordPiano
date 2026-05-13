@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 max-w-full">
    <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Guest Bookings</h2>
            <p class="text-sm text-gray-600">Manage piano lesson bookings and meeting links</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.guest-bookings.availability') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Manage Availability
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Focus/Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting Link</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($bookings as $booking)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $booking->name }}</div>
                        <div class="text-sm text-gray-500">{{ $booking->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}<br>
                        {{ \Carbon\Carbon::parse($booking->time)->format('g:i A') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ ucfirst($booking->skill_level) }}<br>
                        <span class="text-xs italic">{{ $booking->focus }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($booking->payment_status === 'paid')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <form action="{{ route('admin.guest-bookings.update-link', $booking) }}" method="POST" class="flex gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="google_meet_link" value="{{ $booking->google_meet_link }}" 
                                placeholder="https://meet.google.com/..." 
                                class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 w-48">
                            <button type="submit" class="bg-gray-800 text-white px-3 py-1 rounded text-sm hover:bg-gray-900">
                                Save
                            </button>
                        </form>
                        @if($booking->google_meet_link)
                            <a href="{{ $booking->google_meet_link }}" target="_blank" class="text-blue-600 hover:underline text-xs mt-1 block">
                                Join Meeting
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
