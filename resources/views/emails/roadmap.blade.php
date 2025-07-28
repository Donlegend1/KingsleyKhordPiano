@extends('layouts.email')

@section('content')
    <p>Hey {{ $user->first_name }},</p>

    <p>
        How’s your first day feeling so far? I know starting something new can be both exciting and a little intimidating,
        but trust me...
    </p>

    <p><strong>Every great pianist started right where you are.</strong></p>

    <p>To make this as smooth and fun as possible, here’s a simple roadmap:</p>

    <ul>
        <li><strong>First lesson:</strong> Start with <strong>{{ $user->course_name ?? 'your foundation course' }}</strong>, it’ll give you a solid foundation.</li>
        <li><strong>Practice tip:</strong> 15 minutes a day beats 2 hours once a week. Stay consistent.</li>
        <li><strong>Bonus motivation:</strong> Imagine the first song you’ll play effortlessly... how cool will that feel?</li>
    </ul>

    <p>
        You got this, {{ $user->first_name }}.<br>
        Take your time, enjoy the process, and remember...<br>
        <strong>Every note you play is a step forward.</strong>
    </p>

    <p>
        Let me know how it’s going! Just reply to this email anytime.
    </p>

    <p>
        Kingsley<br>
        <em>Your biggest fan.</em>
    </p>
@endsection
