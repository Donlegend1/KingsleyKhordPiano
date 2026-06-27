@extends('layouts.community')

@section('content')
<!-- Header Section -->
<div class="flex justify-between items-center mx-auto border bg-white dark:bg-[#161617] border-gray-200 dark:border-white/10 px-10 py-4">
    
    <!-- Left: Breadcrumb -->
    <div>
        <a 
            href="/member/community/members" 
            class="font-semibold text-gray-800 dark:text-gray-100 hover:underline"
        >
            {{ Str::ucfirst(Str::lower($post->subcategory)) }}
        </a>
    </div>

</div>

<!-- Main Content -->
<!-- Main Feed Section -->
<section class="bg-gray-100 dark:bg-black py-6 px-2 md:px-10 ">
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
