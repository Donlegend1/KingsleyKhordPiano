@extends('layouts.member')

@section('content')

@if(auth()->user()->created_at->diffInDays(now()) <= 7)
    @include("components.memberarea.getstarted")
@endif
@include("components.memberarea.details")
@include("components.memberarea.guide")
@include("components.memberarea.stats")
@include("components.memberarea.liveshow")
@include("components.memberarea.schedule")


@endsection
