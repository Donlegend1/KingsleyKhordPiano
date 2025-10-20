@extends('layouts.email')
@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
<p>Hey {{ $user->first_name }},</p>

<p>
    Great progress doesn’t always come from long practice sessions—it comes from consistent, focused learning.
</p>

<p>
    That’s why we’ve designed <strong>Quick Lessons</strong> that fit easily into your day, no matter your skill level.
</p>

<ul>
    <li><strong>Beginners:</strong> Master the fundamentals step by step.</li>
    <li><strong>Intermediate Players:</strong> Refine your technique and build confidence.</li>
    <li><strong>Advanced Players:</strong> Challenge yourself with new skills and concepts.</li>
</ul>

<p>
    Even 10 minutes a day can make a difference in your playing! Pick a lesson, hit play, and watch your skills grow.
</p>

<p>
    <a href="{{ config('app.url') }}/quick-lessons" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">Start a Quick Lesson</a>
</p>

<p>
    Keep the momentum going. Your future self will thank you!
</p>

<p>Kingsley</p>

</div>
@endsection
