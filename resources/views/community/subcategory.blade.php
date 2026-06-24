@extends('layouts.community')

@section('content')
<!-- Header Section -->
<div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 mb-6">
    <div class="px-6 py-5">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-tight">{{ Str::headline($subcategory) }}</h1>
    </div>
</div>

<!-- Main Content -->
<div class="overflow-y-auto h-screen p-4 bg-gray-50 dark:bg-gray-900">
    <div id="subcategory"></div>
</div>
@endsection
<script>
    window.activeSubscription = @json($activeSubscription);
</script>
