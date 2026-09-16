@extends('layouts.hub')

@section('title', 'Post')

@section('breadcrumbs')
    @include('community.partials.breadcrumbs', ['items' => $breadcrumbs ?? []])
@endsection

@section('content')
<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2">
            <div id="single-post"></div>
        </div>
    </div>
</div>
@endsection
