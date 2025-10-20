@extends('layouts.email')

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
<p>Hey {{ $user->first_name }},</p>

<p>Embarking on your piano journey is exciting, and knowing where to begin can make all the difference.</p>

<p>To tailor your learning experience, could you take a moment to assess your current skill level?</p>

<p>
    <a href="{{ $assessmentLink }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
        Take Assessment
    </a>
</p>

<p>This will help us recommend lessons that fit you just right, ensuring a smooth and enjoyable progression.</p>

<p>Looking forward to seeing where you shine!</p>

<p>– Kingsley</p>
</div>
@endsection
