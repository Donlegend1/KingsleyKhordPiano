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
            <a href="/member/community/user/{{$community->id}}">{{ $community->user_name }}</a>  
            </span>
        </div>

        <!-- Edit Profile Button -->
        <a 
            href="#" 
            class="flex items-center gap-1  rounded-md px-3 py-1 text-sm hover:opacity-90"
        >
        <i class="fa fa-bell" aria-hidden="true"></i>
            Notification Setting
        </a>
    </div>

    <!-- Main Content -->
    <div class="overflow-y-auto h-screen p-4">
    <div id="community-profile-edit">

    </div>
    </div>
@endsection
