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
<div>
<div id="plan-switch" >
   
</div>
</div>

@include("components.journey")

{{-- <div id="zoomMeetingBooking"></div> --}}
@include("components.faq")


<script>
    window.authUser = @json(Auth::user());
</script>
@endsection