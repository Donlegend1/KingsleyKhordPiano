@extends("layouts.app")

@section("content")
@include("components.banner")
@include("components.roadmap")
@include("components.joinplatform")
@include("components.latest-lessons-public")
@include("components.coaches")
{{-- @include("components.joinow2") --}}
{{-- @include("components.courses") --}}

@include("components.subscribe")

@endsection