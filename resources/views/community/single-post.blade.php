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
            {{ Str::ucfirst(Str::lower($post->subcategory)) }}
        </a>
    </div>

    <!-- Right: Links & Ellipsis -->
    <div class="flex items-center gap-4">
        <a 
            href="/member/community/members" 
            class=" bg-gray-200 px-2 py-1 text-gray-700 dark:text-gray-700 hover:underline rounded-md"
        >
            Posts
        </a>

        <a 
            href="/member/community/members" 
            class="font-semibold text-gray-800 dark:text-gray-100 hover:underline"
        >
            Members
        </a>

        <i class="fa fa-ellipsis-v text-gray-500 dark:text-gray-300 cursor-pointer"></i>
    </div>
</div>

<!-- Main Content -->
<!-- Main Feed Section -->
<section class="bg-gray-100 dark:bg-gray-900 py-6 px-2 md:px-10 ">
  <div class="flex flex-col lg:flex-row gap-6 ">

    <!-- Left: Main Feed Area -->
    <div class="flex-1 space-y-6">
      <div class=" rounded-lg p-4">
        <div id="single-post"></div>
      </div>
    </div>

    <!-- Right: Recent Activity -->
    <div class="w-full lg:w-[300px]">
    </div>
</section>

@endsection
