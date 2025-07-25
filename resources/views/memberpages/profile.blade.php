

@php
    $layout = auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.member';
@endphp

@extends($layout)

@section('content')
    <div class="max-w-5xl mx-auto py-10 px-6">
        <h2 class="text-3xl font-semibold text-gray-800 mb-8">My Profile</h2>

    <div x-data="{ open: false }" class="space-y-6">

    <!-- Toggle Button -->
        <div class="flex justify-end">
            <button 
                @click="open = !open"
                class="px-4 py-2 bg-black text-white hover:text-black rounded-md text-sm font-medium hover:bg-[#FFD736] transition"
            >
                <span class="fa fa-edit"></span> Profile
            </button>
        </div>
    
    <!-- Form Section -->
        <div x-show="open" x-transition class="bg-white p-6 rounded-2xl shadow-md space-y-6">

            <form action="/profile/update" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', Auth::user()->first_name) }}" required
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('first_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', Auth::user()->last_name) }}" required
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('last_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', Auth::user()->email) }}" required
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <select id="country" name="country"
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach ($countries as $code => $name)
                                <option value="{{ $code }}" {{ old('country', Auth::user()->country) == $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input id="password" name="password" type="password"
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>

                    <!-- Passport -->
                    <div>
                        <label for="passport" class="block text-sm font-medium text-gray-700 mb-1">Passport</label>
                        <input id="passport" name="passport" type="file"
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('passport')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-black text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Subscriptions --}}

    @if (auth()->user()->role === 'member' )
  @php
    $payment = Auth::user()->payments()->latest()->first();

    $metadata = $payment->metadata ?? [];
    $currency = strtoupper($metadata['currency'] ?? 'USD'); // ensure it's uppercase

    $symbols = [
        'NGN' => '₦',
        'USD' => '$',
        'GBP' => '£',
        'EUR' => '€',
    ];

    $symbol = $symbols[$currency] ?? '';
@endphp

@if ($payment)
    <div class="mt-10 bg-white p-6 rounded-2xl shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Subscriptions</h3>

        <ul class="divide-y divide-gray-200">
            <li class="py-3">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium">
                            Plan: {{ $metadata['tier'] ?? 'Free' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Amount: {{ $symbol }}{{ number_format($payment->amount, 2) }} ({{ $currency }})
                        </p>
                    </div>
                    <a href="#" class="text-indigo-600 hover:underline text-sm">Manage</a>
                </div>
            </li>
        </ul>
    </div>
@else
    <p class="text-sm text-gray-500 mt-6">No subscription found.</p>
@endif


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
                                {{ \Carbon\Carbon::parse($txn->starts_at)->format('M d, Y') }}
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
    @endif
   


    {{-- Delete Account --}}
   @php
    $userEmail = auth()->user()->email;
@endphp

<div x-data="{ showModal: false, deleteEmail: '' }" class="mt-10 bg-red-50 p-6 rounded-2xl shadow-md border border-red-200">
    <h3 class="text-xl font-semibold text-red-600 mb-2">Danger Zone</h3>
    <p class="text-sm text-red-500 mb-4">
        Once you delete your account, there is no going back. Please be certain.
    </p>

    <!-- Trigger Button -->
    <button
        @click="showModal = true"
        type="button"
        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md font-medium text-sm"
    >
        Delete My Account
    </button>

    <!-- Modal -->
    <div
        x-show="showModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        x-cloak
    >
        <div
            @click.away="showModal = false"
            class="bg-white rounded-lg p-6 max-w-sm w-full shadow-lg text-gray-900"
        >
            <h2 class="text-lg font-semibold mb-2">Confirm Deletion</h2>
            <p class="text-sm text-gray-600 mb-4">
                To confirm, please type <strong>{{ $userEmail }}</strong> below.
            </p>

            <input
                type="email"
                x-model="deleteEmail"
                placeholder="Enter your email to confirm"
                class="w-full border rounded-md px-3 py-2 text-sm mb-4"
            />

            <div class="flex justify-end space-x-3">
                <button
                    @click="showModal = false"
                    type="button"
                    class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded-md"
                >
                    Cancel
                </button>

                <form action="/profile/delete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        :disabled="deleteEmail !== '{{ $userEmail }}'"
                        class="px-4 py-2 text-sm text-white rounded-md transition"
                        :class="deleteEmail === '{{ $userEmail }}' ? 'bg-red-600 hover:bg-red-700' : 'bg-red-300 cursor-not-allowed'"
                    >
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


</div>
@endsection
