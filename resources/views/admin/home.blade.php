@extends('layouts.admin')

@section('content')
<main class="flex-1 p-4 sm:p-6 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
  <!-- Header -->
  <header class="mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div>
      <h2 class="text-2xl font-bold text-gray-800">Welcome Back, {{ auth()->user()->first_name }}! 👋</h2>
      <p class="text-gray-600 mt-1">Here's what's happening with your piano academy today.</p>
    </div>

    <div class="flex items-center gap-3 flex-wrap">
      <div class="relative">
        <input 
          type="text" 
          placeholder="Search users..." 
          class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none w-full sm:w-auto"
        >
        <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
      </div>
      <button class="relative bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
        <i class="fa fa-bell mr-2"></i>
        Notifications
        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-xs text-white">2</span>
      </button>
    </div>
  </header>

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Users -->
      <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6 border-b-4 border-blue-500">
          <div class="flex justify-between items-center mb-4">
              <div class="bg-blue-100 text-blue-600 p-3 rounded-xl">
                  <i class="fa fa-users text-xl"></i>
              </div>
              <span class="text-sm {{ $userGrowth >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                  <i class="fa fa-{{ $userGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                  {{ abs($userGrowth) }}%
              </span>
          </div>
          <h4 class="text-gray-500 text-sm font-medium mb-1">Total Users</h4>
          <div class="flex items-end justify-between">
              <div>
                  <h3 class="text-3xl font-bold text-gray-800">{{ number_format($totalUsers) }}</h3>
                  <p class="text-sm text-gray-500 mt-1">
                      <span class="text-green-600">{{ number_format($activeUsers) }}</span> active this week
                  </p>
              </div>
              <p class="text-xs text-gray-500">Last 30 days</p>
          </div>
      </div>

      <!-- Revenue Stats -->
      <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6 border-b-4 border-green-500">
          <div class="flex justify-between items-center mb-4">
              <div class="bg-green-100 text-green-600 p-3 rounded-xl">
                  <i class="fa fa-dollar-sign text-xl"></i>
              </div>
              <span class="text-sm {{ $revenueGrowth >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                  <i class="fa fa-{{ $revenueGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                  {{ abs($revenueGrowth) }}%
              </span>
          </div>
          <h4 class="text-gray-500 text-sm font-medium mb-1">Total Revenue</h4>
          <div class="flex items-end justify-between">
              <div>
                  <h3 class="text-3xl font-bold text-gray-800">${{ number_format($usdRevenue) }}</h3>
                  <div class="flex gap-2 mt-1 text-sm text-gray-500">
                      <span>€{{ number_format($eurRevenue) }}</span>
                      <span>₦{{ number_format($nairaRevenue) }}</span>
                  </div>
              </div>
              <p class="text-xs text-gray-500">This month</p>
          </div>
      </div>

      <!-- Courses -->
      <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow p-6 border-b-4 border-purple-500">
          <div class="flex justify-between items-center mb-4">
              <div class="bg-purple-100 text-purple-600 p-3 rounded-xl">
                  <i class="fa fa-graduation-cap text-xl"></i>
              </div>
              <span class="text-sm {{ $courseGrowth >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center">
                  <i class="fa fa-{{ $courseGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                  {{ abs($courseGrowth) }}%
              </span>
          </div>
          <h4 class="text-gray-500 text-sm font-medium mb-1">Total Courses</h4>
          <div class="flex items-end justify-between">
              <h3 class="text-3xl font-bold text-gray-800">{{ number_format($courses) }}</h3>
              <p class="text-xs text-gray-500">All time</p>
          </div>
      </div>
  </div>

  <!-- Recent Users Table -->
  <div class="bg-white rounded-2xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-bold text-gray-800">Recently Joined Users</h3>
      <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All Users</a>
    </div>
    
    @if($users->isEmpty())
      <div class="text-center py-12">
        <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
          <i class="fa fa-users text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-gray-500 font-medium">No New Users</h3>
        <p class="text-gray-400 text-sm mt-1">No new users registered in the last 2 weeks.</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead>
            <tr class="bg-gray-50 text-gray-600 text-sm">
              <th class="px-6 py-3 text-left font-semibold">#</th>
              <th class="px-6 py-3 text-left font-semibold">User</th>
              <th class="px-6 py-3 text-left font-semibold">Email</th>
              <th class="px-6 py-3 text-left font-semibold">Joined</th>
              <th class="px-6 py-3 text-left font-semibold">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach ($users as $index => $user)
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">{{ $index + 1 }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden">
                      @if($user->passport)
                        <img src="{{ $user->passport }}" alt="{{ $user->first_name }}" class="w-full h-full object-cover">
                      @else
                        <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-500">
                          {{ substr($user->first_name, 0, 1) }}
                        </div>
                      @endif
                    </div>
                    <div>
                      <p class="font-medium text-gray-800">{{ $user->first_name . " " . $user->last_name }}</p>
                      <p class="text-xs text-gray-500">Member</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                <td class="px-6 py-4 text-gray-500">{{ $user->created_at->diffForHumans() }}</td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 text-xs font-medium rounded-full 
                    {{ $user->email_verified_at ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        @if ($users->hasPages())
          <div class="mt-4">
            {{ $users->links('components.pagination') }}
          </div>
        @endif
      </div>
    @endif
  </div>
</main>
@endsection