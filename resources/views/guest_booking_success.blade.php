@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg border-t-4 border-green-500">
        <div class="px-4 py-5 sm:px-6 text-center">
            <div class="mb-4">
                <i class="fa fa-check-circle text-6xl text-green-500"></i>
            </div>
            <h3 class="text-3xl leading-6 font-bold text-gray-900">
                Booking Confirmed!
            </h3>
            <p class="mt-2 max-w-2xl text-sm text-gray-500 mx-auto">
                Thank you, {{ $booking->name }}. Your one-on-one session with Kingsley Khord has been scheduled.
            </p>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        {{ \Carbon\Carbon::parse($booking->date)->format('F j, Y') }}
                    </dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Time</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $booking->time }}</dd>
                </div>
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $booking->email }}</dd>
                </div>
                @if($booking->zoom_join_url)
                <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-blue-50">
                    <dt class="text-sm font-medium text-blue-700">Zoom Link</dt>
                    <dd class="mt-1 text-sm text-blue-900 sm:mt-0 sm:col-span-2">
                        <a href="{{ $booking->zoom_join_url }}" target="_blank" class="font-bold underline hover:text-blue-800">
                            Click here to join meeting
                        </a>
                        <p class="text-xs mt-1 text-blue-600">Please save this link. We've also sent it to your email.</p>
                    </dd>
                </div>
                @endif
            </dl>
        </div>
        <div class="px-4 py-5 sm:px-6 text-center">
            <a href="/" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
