<x-mail::message>
# New Session Booked

A guest just booked and paid for a one-on-one piano coaching session. Here are their details:

<x-mail::panel>
**Name:** {{ $booking->name }}<br>
**Email:** {{ $booking->email }}<br>
**Date:** {{ \Carbon\Carbon::parse($booking->date)->format('l, F j, Y') }}<br>
**Time:** {{ \Carbon\Carbon::parse($booking->date . ' ' . $booking->time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($booking->date . ' ' . $booking->time)->addHour()->format('g:i A') }}<br>
**Payment:** ${{ '60' }} via {{ ucfirst($booking->payment_method) }} ({{ ucfirst($booking->payment_status) }})<br>
@if($booking->skill_level)
**Skill Level:** {{ ucfirst($booking->skill_level) }}<br>
@endif
@if($booking->focus)
**Focus:** {{ $booking->focus }}<br>
@endif
</x-mail::panel>

**Action needed:** Prepare a Zoom meeting link for this session and add it to the booking so it gets sent to {{ $booking->name }} before their session.

<x-mail::button :url="route('admin.guest-bookings.index')">
View Booking in Admin
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
