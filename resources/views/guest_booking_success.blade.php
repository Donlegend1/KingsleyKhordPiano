@extends('layouts.app')

@php
    use Carbon\Carbon;

    $start = Carbon::parse($booking->date . ' ' . $booking->time, 'Africa/Lagos');
    $end = $start->copy()->addHour();

    $title = rawurlencode('Piano Coaching Session with Kingsley Khord');
    $details = rawurlencode('Your one-on-one piano coaching session with Kingsley Khord.');

    $googleCalendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
        . '&text=' . $title
        . '&dates=' . $start->copy()->utc()->format('Ymd\THis\Z') . '/' . $end->copy()->utc()->format('Ymd\THis\Z')
        . '&details=' . $details;

    $outlookCalendarUrl = 'https://outlook.live.com/calendar/0/deeplink/compose'
        . '?subject=' . $title
        . '&startdt=' . $start->copy()->utc()->toIso8601String()
        . '&enddt=' . $end->copy()->utc()->toIso8601String()
        . '&body=' . $details
        . '&path=/calendar/action/compose&rru=addevent';
@endphp

@section('content')
<div class="min-h-screen bg-gray-50 px-4 pt-32 pb-16">
    <div class="max-w-5xl mx-auto bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-5">

            {{-- Left: Session Info --}}
            <div class="lg:col-span-2 p-8 lg:p-10 bg-gray-50 border-r border-gray-100">
                <h3 class="text-xs font-semibold text-blue-600 uppercase tracking-widest">Hosted by</h3>
                <p class="mt-2 text-2xl font-bold text-gray-900 tracking-tight">Kingsley Khord</p>

                <div class="mt-6 divide-y divide-gray-100">
                    <div class="flex items-center gap-3.5 py-3.5 text-gray-700">
                        <svg class="w-[18px] h-[18px] text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-[15px]">1 Hour Session</span>
                    </div>
                    <div class="flex items-center gap-3.5 py-3.5 text-gray-700">
                        <svg class="w-[18px] h-[18px] text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                        <span class="text-[15px]">Zoom (Link provided after booking)</span>
                    </div>
                    <div class="flex items-center gap-3.5 py-3.5 text-gray-900">
                        <svg class="w-[18px] h-[18px] text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5.586a1 1 0 01.707.293l6.414 6.414a1 1 0 010 1.414l-7.586 7.586a1 1 0 01-1.414 0L3.293 12.293A1 1 0 013 11.586V6a3 3 0 013-3z"/>
                        </svg>
                        <span class="text-[15px] font-semibold">$60</span>
                    </div>
                </div>

                {{-- Booking Summary --}}
                <div class="mt-6 bg-white rounded-xl border border-gray-200 p-5">
                    <p class="text-sm font-bold text-gray-900 mb-4">Your Booking Summary</p>

                    <div class="flex items-start gap-3 mb-4">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-400">Date</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $start->format('l, F j, Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 mb-4">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-400">Time</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $start->format('g:i A') }} – {{ $end->format('g:i A') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 mb-4">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-400">Host</p>
                            <p class="text-sm font-semibold text-gray-900">Kingsley Khord</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div class="flex items-center gap-2">
                            <div>
                                <p class="text-xs text-gray-400">Payment</p>
                                <p class="text-sm font-semibold text-gray-900">via {{ ucfirst($booking->payment_method) }}</p>
                            </div>
                            <span class="text-[11px] font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full uppercase">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <p class="mt-5 text-xs text-gray-400 leading-relaxed">
                    A confirmation email has been sent to<br>
                    <span class="font-semibold text-gray-600">{{ $booking->email }}</span>
                </p>
            </div>

            {{-- Right: Confirmation --}}
            <div class="lg:col-span-3 p-8 lg:p-12 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                    Your Meeting is Successfully Booked!
                </h1>
                <p class="mt-2 text-gray-500 max-w-md mx-auto leading-relaxed">
                    Thank you, {{ $booking->name }}! Your coaching session with Kingsley Khord has been confirmed.
                </p>

                @if($booking->zoom_join_url)
                    <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3 text-left">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                        <p class="text-sm text-blue-900">
                            <a href="{{ $booking->zoom_join_url }}" target="_blank" rel="noopener noreferrer" class="font-bold underline hover:text-blue-700">
                                Click here to join your meeting
                            </a>
                            <span class="block text-xs text-blue-600 mt-1">We've also sent this link to your email.</span>
                        </p>
                    </div>
                @else
                    <div class="mt-6 bg-green-50 border border-green-100 rounded-xl p-4 flex items-start gap-3 text-left">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-green-900">
                            The meeting link and all the details have been sent to
                            <span class="font-bold">{{ $booking->email }}</span>. Please check your inbox.
                        </p>
                    </div>
                @endif

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4 text-left">

                    {{-- Add to Calendar --}}
                    <div class="border border-gray-200 rounded-xl p-5">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-900">Add to Your Calendar</p>
                        <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                            Easily add this event to your calendar so you never miss it.
                        </p>
                        <div class="mt-4 space-y-2">
                            <a href="{{ $googleCalendarUrl }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center justify-center gap-2 text-sm font-semibold text-blue-600 border border-blue-200 rounded-lg py-2.5 hover:bg-blue-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Add to Google Calendar
                            </a>
                            <a href="{{ $outlookCalendarUrl }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center justify-center gap-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg py-2.5 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Add to Outlook Calendar
                            </a>
                        </div>
                    </div>

                    {{-- Need Help --}}
                    <div class="border border-gray-200 rounded-xl p-5">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-900">Need Help?</p>
                        <p class="mt-1 text-xs text-gray-500 leading-relaxed">
                            We're here if you have any questions or need assistance.
                        </p>
                        <div class="mt-4">
                            <a href="/contact"
                                class="flex items-center justify-center gap-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-lg py-2.5 hover:bg-gray-50 transition">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Contact Support
                            </a>
                        </div>
                        <p class="mt-3 text-xs text-gray-400">We typically respond within 24 hours.</p>
                    </div>
                </div>

                <p class="mt-10 text-sm text-gray-500">
                    We look forward to a productive and valuable session with you!
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
