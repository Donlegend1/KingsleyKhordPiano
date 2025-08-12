@extends("layouts.community")

@section("content")
@php
    $members = \Illuminate\Support\Facades\DB::table('users')->get();
@endphp

<div class="p-6">
    <!-- Header for members list -->
    <div class="border border-gray-200 dark:border-gray-500 mb-4">
        <div class="flex justify-between items-center px-4 py-2 bg-white dark:bg-gray-800">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                All Members ({{ $members->count() }})
            </h1>
            <i class="fa fa-ellipsis-v text-gray-500 dark:text-gray-300"></i>
        </div>
    </div>

    <!-- Members -->
    <div id="members">
        <!-- Members content -->
    </div>
</div>
@endsection
