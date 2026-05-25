@extends("layouts.app")

@section("content")
@include("components.banner")
@include("components.roadmap")
@include("components.joinow")
@include("components.journey")
<div id="coach-reels"></div>
@include("components.joinow2")
@include("components.plansstats")
@include("components.blogs")
{{-- @include("components.courses") --}}

<div id="zoomMeetingBooking"></div>

@include("components.subscribe")

@endsection