@extends('layouts.community')

@section('content')
<!-- Header Section -->
<div class="flex justify-between items-center mx-auto border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-500 px-10 py-4">
    <!-- Breadcrumb -->
    <div class="flex gap-2 items-center">
        <a 
            href="/member/community/members" 
            class="font-semibold text-gray-800 dark:text-gray-100 hover:underline"
        >
            Members
        </a>
        <span>/</span>
        <span class="text-sm text-gray-600 dark:text-gray-300">
            {{ $community->user_name }}
        </span>
    </div>

    <!-- Edit Profile Button -->
    <a 
        href="/member/community/u/{{$community->id}}/update" 
        class="flex items-center gap-1 bg-black dark:bg-gray-100 rounded-md px-3 py-1 text-white dark:text-black text-sm hover:opacity-90"
    >
        <span class="fa fa-edit"></span>
        Edit Profile
    </a>
</div>

<!-- Main Content -->
<div class="overflow-y-auto h-screen p-4">
   <div id="community-profile">

   </div>
</div>
@endsection
