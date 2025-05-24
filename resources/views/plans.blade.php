@extends("layouts.app")

@section("content")
@include("components.pricebanner")
@include("components.plansstats")
@include("components.features")
@include("components.youtube")
@include("components.encouragement")
@include("components.approach")
@include("components.membership")
@include("components.practise")
{{-- @include("components.price") --}}
<div id="plan-switch" >
   
</div>
@include("components.journey")
{{-- @include("components.memberarea.schedule") --}}
<div id="zoomMeetingBooking"></div>
@include("components.faq")



@endsection