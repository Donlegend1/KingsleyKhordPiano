@extends("layouts.hub")

@section("title", "Activity Feed")

@section("breadcrumbs")
    @include('community.partials.breadcrumbs')
@endsection

@section("content")
<div class="p-4 pt-2 sm:p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <div class="lg:col-span-2">
            <div id="post-list"></div>
        </div>

        @include('community.partials.sidebar')
    </div>
</div>
@endsection
