@extends('layouts.email')

@section('content')

<p><strong>Name:</strong> {{ $data['first_name'] }} {{ $data['last_name'] }}</p>
<p><strong>Email:</strong> {{ $data['email'] }}</p>
<p><strong>Message:</strong></p>
<p>{{ $data['message'] }}</p>

@endsection