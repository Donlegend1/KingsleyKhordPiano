@extends('layouts.admin')

@section('content')

 <main class="flex-1 p-6">
    <header class="mb-6 flex items-center justify-between">
      <h2 class="text-xl font-bold">Dashboard</h2>
      <div class="flex items-center gap-4">
        <input type="text" placeholder="Search..." class="px-4 py-2 border rounded-lg">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
          <i class="fa fa-bell"></i>
        </button>
        <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
      </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold">Users</h3>
        <p class="text-gray-500">{{$users->count()}}</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold">Revenue</h3>
        <p class="text-gray-500">${{$usdRevenue}}</p>
        <p class="text-gray-500">€{{$eurRevenue}}</p>
        <p class="text-gray-500">₦{{$nairaRevenue}}</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow-lg">
        <h3 class="text-lg font-semibold">New Orders</h3>
        <p class="text-gray-500">567</p>
      </div>
    </div>
  </main>

@endsection