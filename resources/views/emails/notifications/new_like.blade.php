@extends('layouts.email')

@section('content')
    <p>Hey {{ $notifiable->first_name }},</p>

  <p>{{ $like->user->first_name }} {{ $like->user->last_name }} liked your post.</p> 

    <a href="{{ $url }}" class="button">View Post</a>

    <p>
        Keep the conversation going, and thank you for being an active member of our community!
    </p>

    <p>
        Best regards,<br>
        Kingsley<br>
        <em>Your biggest fan.</em>
    </p>

@endsection