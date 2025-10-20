@extends('layouts.email')

@section('content')

<h2>New Contact Message from {{ $name }}</h2>

    <p><strong>Subject:</strong> {{ $subject }}</p>

    <p><strong>Message:</strong></p>
    <p>{{ $messageContent }}</p>

    <hr>
    <p>
        <strong>Sender Details:</strong><br>
        Name: {{ $name }}<br>
        Email: {{ $email }}
    </p>

    <p style="font-size: 12px; color: #666;">This message was sent from your contact form on Kingsley Khord Piano.</p>

@endsection