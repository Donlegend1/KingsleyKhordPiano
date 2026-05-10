@extends('layouts.email')

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
    <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
        <h2 style="color: #111;">Hi {{ $user->first_name ?? 'there' }},</h2>

        <p>Thanks for being part of Kingsleykhord Piano Academy. Your membership has been successfully cancelled.</p>

        <p>We’re sorry to see you go, and if you don’t mind, we’d love to know why you chose to leave, it helps us do better.</p>

        <p>Whenever you're ready to return, we’ll be here. Until then, keep playing and stay inspired.</p>

        <p style="margin-top: 24px;">
            Best regards,<br>
            <strong>Kingsley</strong>
        </p>
    </div>
</div>
@endsection
