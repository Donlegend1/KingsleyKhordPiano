@extends('layouts.community')

@section('content')
<!-- Header Section -->
<div class="flex justify-between items-center mx-auto border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-500 px-10 py-4">
    
    <!-- Left: Breadcrumb -->
    <div>
        <a 
            href="/member/community/members" 
            class="font-semibold text-gray-800 dark:text-gray-100 hover:underline"
        >
            {{ Str::ucfirst(Str::lower($subcategory)) }}
        </a>
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
