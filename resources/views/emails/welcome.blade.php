@extends('layouts.email')

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
    <p>Hey {{ $user->first_name }},</p>

    <p>
        I just wanted to take a moment to personally welcome you to <strong>Kingsleykhord Piano Academy</strong>.
        You’re not just another student here. You’re now part of a passionate, music-loving family.
    </p>

    <p>
        Learning the piano is more than just hitting the right notes. It’s about <strong>expression</strong>,
        <strong>joy</strong>, and finding a <strong>rhythm that feels like home</strong>. And I can’t wait to see you
        grow into the pianist you’re meant to be.
    </p>

    <p>Your first lesson is waiting for you! Dive in whenever you’re ready:</p>

    <a href="{{ config('app.url') . '/login' }}" class="button">Start Learning</a>

    <p>
        If you ever feel stuck or just want to share a win (big or small), hit ‘Reply.’ I’d love to hear from you!
    </p>

    <p>
        Welcome aboard, {{ $user->first_name }}.<br>
        <strong>You’re in for something special.</strong>
    </p>

    <p>
        Kingsley<br>
        <em>Your biggest fan.</em>
    </p>
</div>
@endsection
