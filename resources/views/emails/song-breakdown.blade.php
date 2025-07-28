@extends('layouts.email')

@section('content')

<p>Hey {{ $user->first_name }},</p>

<p>Every great musician has one thing in common—a rich musical vocabulary. The more you understand the structure, techniques, and emotions behind a song, the better you become at playing, composing, and expressing yourself on the piano.</p>

<p>That’s why we’ve created detailed song breakdowns for every skill level:</p>

<ul>
    <li><strong>Beginners:</strong> Learn simple melodies, rhythms, and hand coordination.</li>
    <li><strong>Intermediate:</strong> Explore chord progressions, dynamics, and phrasing.</li>
    <li><strong>Advanced:</strong> Dive into complex arrangements, improvisation, and stylistic nuances.</li>
</ul>

<p>
    <a href="{{ config('app.url') }}" style="color: #3366cc;">Start exploring today →</a>
    
   
 {{-- <a href="{{ config('app.url') }}/song-breakdowns" style="color: #3366cc;">Start exploring today →</a> --}}
</p>

<p>Each breakdown helps you understand the "why" behind the music, making you a more confident and expressive player.</p>

<p>Can’t wait to hear what you create!</p>

<p>– Kingsley</p>
@endsection
