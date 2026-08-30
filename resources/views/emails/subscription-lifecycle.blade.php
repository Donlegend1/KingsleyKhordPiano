@extends('layouts.email')

@php
    $firstName = $user->first_name ?? 'there';
    $tier = strtolower((string) ($plan->tier ?? data_get($user->metadata, 'tier', $user->premium ? 'premium' : 'standard')));
    $planLabel = str_contains($tier, 'premium') ? 'Premium' : 'Standard';
    $duration = strtolower((string) ($plan->type ?? data_get($user->metadata, 'duration', $user->subscription_type ?? 'monthly')));
    $durationLabel = match ($duration) {
        'quarter', 'quarterly' => 'quarterly',
        'year', 'yearly' => 'yearly',
        default => 'monthly',
    };
    $renewsOn = $user->subscription_expires_at
        ? $user->subscription_expires_at->format('F j, Y')
        : null;
    $dashboardUrl = url('/home');
@endphp

@section('content')
<div style="max-width: 480px; margin: 0 auto; padding: 12px; background: #fff; border-radius: 8px;">
    <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
        <h2 style="color: #111;">Hi {{ $firstName }},</h2>

        @if($isRenewal)
            <p>Your <strong>{{ $planLabel }}</strong> membership has been renewed successfully. You still have full access to your lessons, practice tools, and community.</p>
        @else
            <p>Thank you for joining Kingsleykhord Piano Academy. Your <strong>{{ $planLabel }}</strong> ({{ $durationLabel }}) membership is now active, and you’re part of the family.</p>
            <p>Your lessons are waiting for you — dive in whenever you’re ready.</p>
        @endif

        @if($renewsOn)
            <p>Your current billing period continues through <strong>{{ $renewsOn }}</strong>.</p>
        @endif

        <p style="margin-top: 24px;">
            <a href="{{ $dashboardUrl }}" style="display:inline-block; padding:12px 20px; background-color:#1d4ed8; color:#ffffff; text-decoration:none; border-radius:6px;">
                Go to your dashboard
            </a>
        </p>

        <p style="margin-top: 24px;">
            If you have any questions, just reply to this email — I’d love to hear from you.
        </p>

        <p style="margin-top: 24px;">
            Keep playing,<br>
            <strong>Kingsley</strong>
        </p>
    </div>
</div>
@endsection
