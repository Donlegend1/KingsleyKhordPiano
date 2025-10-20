@extends('layouts.email')

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
<p>Hey {{ $user->first_name }},</p>

<p>I just wanted to drop in with a little reminder: <strong>You’re doing amazing.</strong></p>

<p>Learning something new takes courage, patience, and a whole lot of heart. And you, my friend, have all of that in you.</p>

<p>So, if today’s practice felt a little off, or if your fingers fumbled over the keys — breathe. Every pianist you admire once struggled too. But they kept going. And you will too.</p>

<p><strong>Music loves you back, {{ $user->first_name }}.</strong> Keep playing.</p>

<p>— Kingsley</p>

</div>
@endsection
