@extends("layouts.member")

@section("content")

<!-- Header Section -->
<div class="border border-gray-200 dark:border-gray-500">
    <div class="flex justify-between items-center px-10 py-2 bg-white dark:bg-gray-800">
      <p><span class="fa fa-chat"></span></p>
        {{-- <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Premium Room</h1> --}}
        <i class="fa fa-ellipsis-v text-gray-500 dark:text-gray-300" aria-hidden="true"></i>
    </div>
</div>

<div class="overflow-y-scroll">

<!-- Main Feed Section -->
<section class="bg-gray-100 dark:bg-gray-900 py-6 px-2 md:px-10 ">
  <div class="flex flex-col lg:flex-row gap-6 ">


    <!-- Left: Main Feed Area -->
    <div class="flex-1 space-y-6">
      <div class=" rounded-lg p-4">
        <div id="premium-chat"></div>
      </div>
    </div>
  </div>
</section>

</div>

@endsection

