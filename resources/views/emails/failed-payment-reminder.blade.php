@extends('layouts.email')

@section('content')
<p style="font-size: 18px;">Hey {{ $user->first_name }},</p>

<p style="font-size: 16px; margin: 16px 0;">
    We noticed that your recent payment attempt didn’t go through, and we wouldn’t want you to miss out on your lessons.
</p>

<p style="font-size: 16px; margin: 16px 0;">
    Let’s get you back on track so you don’t lose access to your progress, practice tools, and upcoming sessions.
</p>

<div style="text-align: center; margin: 32px 0;">
    <a href="{{ config('app.url') }}/member/plan" style="background-color: #e74c3c; color: white; padding: 12px 24px; border-radius: 6px; font-size: 16px; text-decoration: none;">
        💳 Fix Payment Now
    </a>
</div>

<p style="font-size: 16px; margin: 16px 0;">
    If you need help updating your payment info or have questions, feel free to reply to this email—we’re happy to help.
</p>

<p style="font-size: 16px;">All the best,<br>Kingsley</p>
@endsection
