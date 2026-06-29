@extends('layouts.community')

@section('page-title')
    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight truncate">{{ Str::headline($subcategory) }}</h1>
@endsection

@section('content')

<!-- Main Content -->
<div class="overflow-y-auto h-screen p-4 bg-gray-50 dark:bg-black">
    <div id="subcategory"></div>
</div>
@endsection
<script>
    window.activeSubscription = @json($activeSubscription);
</script>
