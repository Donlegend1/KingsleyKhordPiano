<x-mail::message>
# Hi {{ $booking->name }},

Thank you for booking a one-on-one piano coaching session with Kingsley Khord! Your session is confirmed — here's a summary of your booking:

<x-mail::panel>
**Date:** {{ \Carbon\Carbon::parse($booking->date)->format('l, F j, Y') }}<br>
**Time:** {{ \Carbon\Carbon::parse($booking->date . ' ' . $booking->time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($booking->date . ' ' . $booking->time)->addHour()->format('g:i A') }}<br>
**Host:** Kingsley Khord<br>
**Platform:** Zoom<br>
**Duration:** 1 Hour<br>
**Price:** $60 (Paid via {{ ucfirst($booking->payment_method) }})<br>
@if($booking->skill_level)
**Skill Level:** {{ ucfirst($booking->skill_level) }}<br>
@endif
@if($booking->focus)
**Focus:** {{ $booking->focus }}<br>
@endif
</x-mail::panel>

### Your Meeting Link

@if($booking->zoom_join_url)
You can join the session directly using the button below:

<x-mail::button :url="$booking->zoom_join_url">
Join Zoom Meeting
</x-mail::button>
@elseif($booking->google_meet_link)
You can join the session directly using the button below:

<x-mail::button :url="$booking->google_meet_link">
Join Google Meet
</x-mail::button>
@else
Your Zoom meeting link is being prepared and **will be sent to you in a separate email soon** — please keep an eye on your inbox in the days leading up to your session.
@endif

We look forward to a productive and valuable session with you!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
