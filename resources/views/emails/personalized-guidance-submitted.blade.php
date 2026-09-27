@extends('layouts.email')

@section('content')

@php
    $user = $guidanceRequest->user;
    $name = $user->full_name ?? 'Unknown member';
@endphp

<h2 style="color: #222; margin-bottom: 12px;">New Personalized Guidance Request</h2>

<p>A premium member just submitted the personalized guidance form. Here are the details:</p>

<table width="100%" cellpadding="10" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; margin: 20px 0; border-left: 4px solid #1d4ed8; font-size: 15px;">
    <tr>
        <td style="padding: 12px 15px; color: #555; line-height: 1.8;">
            <strong>Name:</strong> {{ $name }}<br>
            <strong>Email:</strong> {{ $user->email ?? '—' }}<br>
            <strong>Assessment video:</strong> <a href="{{ $guidanceRequest->youtube_link }}">{{ $guidanceRequest->youtube_link }}</a><br>
            <strong>Archetype:</strong> {{ $guidanceRequest->archetype }}<br>
            <strong>Experience:</strong> {{ $guidanceRequest->experience_level }}<br>
            <strong>Chord vocabulary:</strong> {{ $guidanceRequest->chord_vocabulary }}<br>
            <strong>Playing by ear:</strong> {{ $guidanceRequest->playing_by_ear }}<br>
            <strong>All 12 keys:</strong> {{ $guidanceRequest->key_fluency }}<br>
            <strong>Wants to play like:</strong> {{ $guidanceRequest->inspiration_pianist }}<br>
            <strong>Practice days per week:</strong> {{ $guidanceRequest->practice_days_per_week }}<br>
            <strong>Practice time per day:</strong> {{ $guidanceRequest->practice_time_per_day }}<br>
            <strong>Style focus:</strong> {{ $guidanceRequest->style_focus }}
        </td>
    </tr>
</table>

<p style="margin-bottom: 4px;"><strong>Primary goal</strong></p>
<p style="white-space: pre-line;">{{ $guidanceRequest->primary_goal }}</p>

@if($guidanceRequest->details)
    <p style="margin-bottom: 4px;"><strong>Additional notes</strong></p>
    <p style="white-space: pre-line;">{{ $guidanceRequest->details }}</p>
@endif

<div style="text-align: center; margin: 25px 0;">
    <a href="{{ route('admin.personalized-guidance.show', $user) }}" style="background-color: #1d4ed8; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 6px; display: inline-block; font-weight: bold;">View request in admin</a>
</div>

<p>Thanks,<br><strong>{{ config('app.name') }}</strong></p>

@endsection
