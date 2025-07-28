@extends('layouts.email')

@section('content')
<x-mail::message>
 
# Hello {{ $user->first_name }},

Your subscription expired on **{{ $payment->ends_at->format('F j, Y') }}**.

To continue enjoying our services, please login to your dashboard to renew your subscription.

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>

@endsection
