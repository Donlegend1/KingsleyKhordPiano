@extends('layouts.community')

@section('breadcrumb-parent', 'Overview')
@section('breadcrumb-parent-url', '/member/my-library')
@section('breadcrumb', Str::headline($subcategory))

@section('content')

<!-- Main Content -->
<div class="overflow-y-auto h-screen p-4 bg-gray-50 dark:bg-black">
    <div id="subcategory"></div>
</div>
@endsection
<script>
    window.activeSubscription = @json($activeSubscription);
</script>
