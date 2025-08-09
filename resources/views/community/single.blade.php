@extends("layouts.community")

@section("content")

<!-- Header Section -->
<div class="border border-gray-200 dark:border-gray-500">
    <div class="flex justify-between items-center px-10 py-2 bg-white dark:bg-gray-800">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Members</h1>
        <i class="fa fa-ellipsis-v text-gray-500 dark:text-gray-300" aria-hidden="true"></i>
    </div>
</div>

<div class="overflow-scroll h-screen">
  <!-- Quick Links Navigation -->
<div class="flex justify-between items-center px-10 py-2 bg-white dark:bg-gray-800 border border-t-0 border-gray-200 dark:border-gray-500">
    <div class="flex space-x-6 text-gray-500 dark:text-gray-200 text-sm">
        <a href="/home" class="hover:text-gray-700">🏠 Dashboard</a>
        <a href="/member/roadmap" class="hover:text-gray-700">🗺️ Roadmap</a>
        <a href="//member/plugins" class="hover:text-gray-700">📁 Download</a>
    </div>
</div>

</div>




@endsection
