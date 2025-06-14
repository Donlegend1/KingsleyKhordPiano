@extends('layouts.admin')

@section('content')

<main class="flex-1 p-6 bg-gray-50 min-h-screen">
  <!-- Header -->
  <header class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    <div class="flex items-center gap-4">
      <input 
        type="text" 
        placeholder="Search..." 
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none"
      >
      <button class="relative bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        <i class="fa fa-bell"></i>
        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
      </button>
      <div class="w-10 h-10 bg-gray-300 rounded-full overflow-hidden">
        <img src="/images/admin-avatar.png" alt="Admin" class="object-cover w-full h-full">
      </div>
    </div>
  </header>

  <!-- Stat Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <!-- Users Card -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition">
      <div class="flex items-center gap-4">
        <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
          <i class="fa fa-users text-xl"></i>
        </div>
        <div>
          <h3 class="text-gray-700 text-sm uppercase font-semibold">Users</h3>
          <p class="text-2xl font-bold text-gray-900">{{ $users->count() }}</p>
        </div>
      </div>
    </div>

    <!-- Revenue Card -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition">
      <div class="flex items-center gap-4">
        <div class="bg-green-100 text-green-600 p-3 rounded-full">
          <i class="fa fa-dollar-sign text-xl"></i>
        </div>
        <div>
          <h3 class="text-gray-700 text-sm uppercase font-semibold">Total Revenue</h3>
          <p class="text-xl font-bold text-gray-900">${{number_format( $usdRevenue) }}</p>
          <p class="text-sm text-gray-500">€{{ number_format($eurRevenue) }} | ₦{{number_format( $nairaRevenue) }}</p>
        </div>
      </div>
    </div>

    <!-- Orders Card -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition">
      <div class="flex items-center gap-4">
        <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
          <i class="fa fa-video text-xl"></i>
        </div>
        <div>
          <h3 class="text-gray-700 text-sm uppercase font-semibold">Courses</h3>
          <p class="text-2xl font-bold text-gray-900">{{$courses}}</p>
        </div>
      </div>
    </div>

  </div>
</main>

@endsection
