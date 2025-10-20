@extends('layouts.email')

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
    <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
        <h2 style="color: #111;">Hello {{ $user->first_name }},</h2>

        <p>Your subscription expired on 
            <strong>{{ $payment->ends_at->format('F j, Y') }}</strong>.
        </p>

        <p>
            To continue enjoying our services, please log in to your dashboard to renew your subscription.
        </p>

        <p style="margin-top: 24px;">
            Thanks,<br>
            <strong>{{ config('app.name') }}</strong>
        </p>
    </div>
</div>
@endsection
