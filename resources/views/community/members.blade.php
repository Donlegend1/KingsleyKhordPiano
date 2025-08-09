@extends("layouts.community")

@section("content")
 @php
$members = \Illuminate\Support\Facades\DB::table('users')->get();
 @endphp

<!-- Header Section -->
<div class="border border-gray-200 dark:border-gray-500">
    <div class="flex justify-between items-center px-10 py-2 bg-white dark:bg-gray-800">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">All Members ({{$members->count()}})</h1>
        <i class="fa fa-ellipsis-v text-gray-500 dark:text-gray-300" aria-hidden="true"></i>
    </div>
</div>
<section class="bg-gray-100 dark:bg-gray-900 py-6 px-2 md:px-10 ">

<div class="h-screen">

    <div id="members" class="my-5 mx-3">

    </div>


</div>
</section>







@endsection
