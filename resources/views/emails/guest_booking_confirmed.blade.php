<x-mail::message>
# Hi {{ $booking->name }},

Your one-on-one piano lesson with Kingsley Khord is confirmed!

Here are the details of your session:

**Date:** {{ \Carbon\Carbon::parse($booking->date)->format('F j, Y') }}  
**Time:** {{ \Carbon\Carbon::parse($booking->time)->format('g:i A') }}  
**Skill Level:** {{ ucfirst($booking->skill_level) }}  
**Focus:** {{ $booking->focus ?? 'General piano lesson' }}  

### Meeting Link

You can join the session directly using the link below:

@if($booking->google_meet_link)
<x-mail::button :url="$booking->google_meet_link">
Join Google Meet
</x-mail::button>
@endif

@if($booking->zoom_join_url)
<x-mail::button :url="$booking->zoom_join_url">
Join Zoom Meeting
</x-mail::button>
@endif

@if(!$booking->google_meet_link && !$booking->zoom_join_url)
<p style="color: #666; font-style: italic;">The meeting link will be provided by the instructor shortly. You will receive another email once it is available.</p>
<x-mail::button :url="'#'">
Meeting Link (Pending)
</x-mail::button>
@endif

We look forward to seeing you there!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
