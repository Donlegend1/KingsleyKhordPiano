@extends('layouts.email')

@section('content')

<p>Hey {{ $user->first_name ?: $user->full_name }},</p>

<p>Your personalized guidance is ready. I put together a plan based on your goals and where you are right now.</p>

<div style="text-align: center; margin: 25px 0;">
    <a href="{{ route('member.personalized-plan') }}" style="background-color: #1d4ed8; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block; font-weight: bold;">View your plan</a>
</div>

<p>Kingsley</p>

@endsection
