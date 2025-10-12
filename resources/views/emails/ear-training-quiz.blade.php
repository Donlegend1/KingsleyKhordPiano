@extends('layouts.email')

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
<p style="font-size: 18px; margin-bottom: 16px;">
    Hey {{ $user->first_name }},
</p>

<p style="font-size: 16px; margin-bottom: 16px;">
    A great pianist doesn’t just play—they <strong>listen</strong>.
</p>

<p style="font-size: 16px; margin-bottom: 16px;">
    Developing a sharp musical ear helps you recognize melodies, harmonies, and rhythms instinctively, making your playing smoother, more expressive, and more natural.
</p>

<p style="font-size: 16px; margin-bottom: 16px;">
    That’s why <strong>ear training</strong> is essential for every musician:
</p>

<ul style="font-size: 16px; padding-left: 18px; margin-bottom: 16px;">
    <li>🎧 Play by ear with confidence</li>
    <li>🎵 Recognize chords, intervals, and melodies faster</li>
    <li>🕒 Improve timing and musical intuition</li>
</ul>

<p style="font-size: 16px; margin-bottom: 24px;">
    Our <strong>Ear Training Quiz</strong> is a fun and effective way to strengthen your listening skills.
</p>

<div style="text-align: center; margin: 32px 0;">
    <a href="{{ config('app.url') }}/member/ear-training"  style="background-color: #4A90E2; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; display: inline-block; font-size: 16px;">
        🎧 Take the Quiz Now
    </a>
</div>

<p style="font-size: 16px; margin-bottom: 24px;">
    The more you train your ears, the more effortlessly you’ll bring music to life.
</p>

<p style="font-size: 16px;">
    Happy listening!<br>
    Kingsley
</p>
</div>
@endsection
