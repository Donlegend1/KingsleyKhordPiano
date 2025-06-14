@extends('layouts.member')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6">
    <h2 class="text-3xl font-semibold text-gray-800 mb-8">My Profile</h2>

    {{-- Profile Info Form --}}
<form action="/profile/update" method="POST" class="bg-white p-6 rounded-2xl shadow-md space-y-6">
    @csrf
    @method('PUT')

    {{-- Display all validation errors at the top --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', Auth::user()->email) }}" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium">New Password</label>
            <input type="password" name="password" id="password"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit"
                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-medium text-sm">
            Save Changes
        </button>
    </div>
</form>


    {{-- Subscriptions --}}
<div class="mt-10 bg-white p-6 rounded-2xl shadow-md">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Subscriptions</h3>

@php
    $metadata = Auth::user()->metadata ?? [];
    $currency = $metadata['currency'] ?? 'USD';

    $symbols = [
        'NGN' => '₦',
        'USD' => '$',
        'GBP' => '£',
        'EUR' => '€',
    ];

    $symbol = $symbols[$currency] ?? '';
@endphp

<ul class="divide-y divide-gray-200">
    <li class="py-3">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium">
                    Plan: {{ $metadata['tier'] ?? 'Free' }}
                </p>
                <p class="text-xs text-gray-500">
                    Amount: {{ $symbol }}{{ number_format(Auth::user()->last_payment_amount, 2) }} ({{ $currency }})
                </p>
            </div>
            <a href="#" class="text-indigo-600 hover:underline text-sm">Manage</a>
        </div>
    </li>
</ul>

</div>

    {{-- Transactions --}}
    <div class="mt-10 bg-white p-6 rounded-2xl shadow-md">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Transactions</h3>

    @if($transactions->isEmpty())
        <p class="text-gray-500 text-sm">No transactions found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                       <th class="px-4 py-2 font-medium">S/N</th>
                        <th class="px-4 py-2 font-medium">Plan</th>
                        <th class="px-4 py-2 font-medium">Amount</th>
                        <th class="px-4 py-2 font-medium">Payment Date</th>
                        <th class="px-4 py-2 font-medium">Status</th>
                    </tr>
                </thead>
               <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($transactions as $txn)
                    @php
                        $metadata = $txn->metadata ?? [];
                        $symbols = ['NGN' => '₦', 'USD' => '$', 'EUR' => '€', 'GBP' => '£'];
                        $currency = $metadata['currency'] ?? 'USD';
                        $symbol = $symbols[$currency] ?? '';
                    @endphp
                    <tr>
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2">{{ $metadata['tier'] ?? 'Free' }}</td>
                        <td class="px-4 py-2">{{ $symbol }}{{ number_format($txn->amount, 2) }}</td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($txn->payment_date)->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium 
                                {{ $txn->status === 'successful' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($txn->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
             </tbody>
             </table>
        </div>
    @endif
</div>


    {{-- Delete Account --}}
    <div class="mt-10 bg-red-50 p-6 rounded-2xl shadow-md border border-red-200">
        <h3 class="text-xl font-semibold text-red-600 mb-2">Danger Zone</h3>
        <p class="text-sm text-red-500 mb-4">Once you delete your account, there is no going back. Please be certain.</p>

        <form action="/profile/delete" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md font-medium text-sm">
                Delete My Account
            </button>
        </form>
    </div>
</div>
@endsection
