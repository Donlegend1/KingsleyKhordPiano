@extends('layouts.admin')

@section('content')
<div class="p-4 sm:p-6 max-w-full">
    <header class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-1">Manage Availability</h2>
            <a href="{{ route('admin.guest-bookings.index') }}" class="text-blue-600 hover:underline text-sm">Back to Bookings</a>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Add Availability Form -->
        <div class="bg-white p-6 rounded-lg shadow h-fit">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Add Available Slot</h3>
            <form action="{{ route('admin.guest-bookings.store-availability') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" min="{{ date('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                    <input type="time" name="time" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Add Slot
                </button>
            </form>
        </div>

        <!-- Availabilities List -->
        <div class="md:col-span-2 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @forelse($availabilities as $date => $slots)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                        <h4 class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($date)->format('l, M d, Y') }}</h4>
                    </div>
                    <div class="p-4 flex flex-wrap gap-3">
                        @foreach($slots as $slot)
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $slot->is_booked ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }} border {{ $slot->is_booked ? 'border-red-100' : 'border-green-100' }}">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($slot->time)->format('g:i A') }}</span>
                                @if($slot->is_booked)
                                    <span class="text-[10px] uppercase font-bold">(Booked)</span>
                                @else
                                    <form action="{{ route('admin.guest-bookings.destroy-availability', $slot) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 focus:outline-none">
                                            <i class="fa fa-times-circle"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-lg shadow text-center text-gray-500">
                    No availability slots set. Use the form to add some!
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
