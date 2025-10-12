@extends('layouts.email')

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
    <p>Hey {{ $notifiable->first_name }},</p>

    <p>
        A new comment has been posted on your post titled "<strong>{{ $comment->post->body }}</strong>".
    </p>

    <p>
        <strong>{{ $comment->user->first_name }}:</strong> {{ $comment->body }}
    </p>

    <p>
        You can view the comment and respond by clicking the button below:
    </p>

    <a href="{{ $url }}" class="button">View Comment</a>

    <p>
        Keep the conversation going, and thank you for being an active member of our community!
    </p>

    <p>
        Best regards,<br>
        Kingsley<br>
        <em>Your biggest fan.</em>
    </p>
</div>
@endsection