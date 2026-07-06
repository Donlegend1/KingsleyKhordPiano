

@php
    $layout = auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.member';
@endphp

@extends($layout)

@section('content')
    <div class="max-w-5xl mx-auto py-10 px-6">
        <h2 class="text-3xl font-semibold text-gray-800 mb-8">My Profile</h2>

    <div x-data="{ open: false }" class="space-y-6">

    @php
        $latestAssessment = \App\Models\UserAssessment::where('user_id', Auth::id())->latest()->first();
    @endphp

    <!-- Account Details Preview -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">My Account Details</h3>
                <button
                    @click="open = !open"
                    class="flex items-center gap-1.5 text-sm font-medium text-[#6366F1] hover:text-[#4F46E5] transition"
                >
                    <span class="fa" :class="open ? 'fa-times' : 'fa-pencil'"></span>
                    <span x-text="open ? 'Cancel' : 'Update'"></span>
                </button>
            </div>

            <dl x-show="!open" class="divide-y divide-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 px-6 py-4 gap-1 sm:gap-4">
                    <dt class="text-sm font-semibold text-gray-700">Account Email</dt>
                    <dd class="text-sm text-gray-600">{{ Auth::user()->email }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 px-6 py-4 gap-1 sm:gap-4">
                    <dt class="text-sm font-semibold text-gray-700">First Name</dt>
                    <dd class="text-sm text-gray-600">{{ Auth::user()->first_name ?: '—' }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 px-6 py-4 gap-1 sm:gap-4">
                    <dt class="text-sm font-semibold text-gray-700">Last Name</dt>
                    <dd class="text-sm text-gray-600">{{ Auth::user()->last_name ?: '—' }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 px-6 py-4 gap-1 sm:gap-4">
                    <dt class="text-sm font-semibold text-gray-700">Skill Level</dt>
                    <dd class="text-sm text-gray-600">{{ $latestAssessment->skill_level ?? 'Nil' }}</dd>
                </div>
            </dl>
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
                    <!-- Passport / Profile Photo -->
                    <div class="col-span-1 md:col-span-2 flex justify-center mb-2"
                        x-data="{ preview: {{ \Illuminate\Support\Js::from(Auth::user()->passport ?: '/avatar1.jpg') }} }">
                        <div class="relative w-28 h-28">
                            <img :src="preview" alt="Profile photo"
                                class="w-28 h-28 rounded-full object-cover border-4 border-gray-100 shadow-sm">

                            <label for="passport"
                                class="absolute bottom-0 right-0 flex items-center justify-center w-9 h-9 rounded-full bg-black text-white shadow-md cursor-pointer hover:bg-gray-800 transition">
                                <span class="fa fa-camera text-sm"></span>
                            </label>

                            <input id="passport" name="passport" type="file" accept="image/*" class="hidden"
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) { preview = URL.createObjectURL(file); }
                                " />
                        </div>
                        @error('passport')
                            <p class="text-red-600 text-sm mt-1 text-center">{{ $message }}</p>
                        @enderror
                    </div>

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
                        <label for="new_password_input" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input id="new_password_input" name="new_password_input" type="password"
                        autocomplete="new-password"
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                        @error('new_password_input')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="new_password_input_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input id="new_password_input_confirmation" name="new_password_input_confirmation" type="password"
                        autocomplete="new-password"
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                    </div>

                </div>

                <!-- Biography Section -->
                <div class="mt-6">
                    <label for="biography" class="block text-sm font-medium text-gray-700 mb-1">Biography</label>
                    <textarea id="biography" name="biography" rows="4" maxlength="1000"
                        class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Tell us about yourself...">{{ old('biography', Auth::user()->biography) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <span id="char-count">0</span>/1000 characters
                    </p>
                    @error('biography')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-black text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Save Changes
                    </button>
                </div>
            </form>

            <!-- Character Count Script -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const textarea = document.getElementById('biography');
                    const charCount = document.getElementById('char-count');

                    function updateCharCount() {
                        const count = textarea.value.length;
                        charCount.textContent = count;

                        // Change color based on character count
                        if (count > 900) {
                            charCount.style.color = '#dc2626'; // red-600
                        } else if (count > 800) {
                            charCount.style.color = '#d97706'; // amber-600
                        } else {
                            charCount.style.color = '#6b7280'; // gray-500
                        }
                    }

                    // Initial count
                    updateCharCount();

                    // Update on input
                    textarea.addEventListener('input', updateCharCount);
                });
            </script>
        </div>
    </div>

    {{-- Subscriptions --}}

    @if (auth()->user()->role === 'member' )
  @php
        $latestSubscription = $transactions->first();
    @endphp

    @if ($latestSubscription)
        @php
            $symbols = ['NGN' => '₦', 'USD' => '$', 'EUR' => '€', 'GBP' => '£'];
            $symbol = $symbols[$latestSubscription->currency] ?? '';
            $user = auth()->user();

            if ($latestSubscription->interval === 'month') {
                $planName = $user->is_premium
                    ? 'Monthly Premium Plan'
                    : 'Monthly Standard Plan';
            } elseif ($latestSubscription->interval === 'year') {
                $planName = $user->is_premium
                    ? 'Yearly Premium Plan'
                    : 'Yearly Standard Plan';
            } else {
                $planName = 'Standard Plan';
            }
        @endphp

        <!-- Latest Subscription Card -->
        <div 
            x-data="{ openModal: false }"
            class="mt-10 bg-white p-6 rounded-2xl shadow-md"
        >
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Active Subscription</h3>

            <ul class="divide-y divide-gray-200">
                <li class="py-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium">
                                Plan: {{ $planName }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Amount: {{ $symbol }}{{ number_format($latestSubscription->amount, 2) }} / {{ ucfirst($latestSubscription->interval) }}
                            </p>
                        </div>

                        @if (auth()->user()->subscription('default') && auth()->user()->subscription('default')->active() && !auth()->user()->subscription('default')->onGracePeriod())
                            <button 
                                @click="openModal = true"
                                class="text-indigo-600 hover:underline text-sm"
                            >
                                Manage
                            </button>
                        @endif
                    </div>
                </li>
            </ul>

            <!-- Manage Subscription Modal -->
           @if (auth()->user()->subscription('default') && auth()->user()->subscription('default')->active() && !auth()->user()->subscription('default')->onGracePeriod())
            {{-- Modal for managing active subscription --}}
            <div 
                x-show="openModal"
                x-cloak
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
            >
                <div 
                    @click.away="openModal = false"
                    class="bg-white rounded-xl shadow-lg w-full max-w-md p-6"
                >
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Manage Subscription</h2>
                    <p class="text-sm text-gray-600 mb-6">
                        You are currently on the <strong>{{ $planName }}</strong>.<br>
                        Do you want to cancel your subscription?
                    </p>

                    <div class="flex justify-end space-x-3">
                        <button 
                            @click="openModal = false"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
                        >
                            Close
                        </button>

                        <form 
                            method="POST" 
                            action="{{ route('subscription.cancel') }}"
                        >
                            @csrf
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                            >
                                Cancel Subscription
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @elseif (auth()->user()->subscription('default') && auth()->user()->subscription('default')->onGracePeriod())
                {{-- Subscription canceled but on grace period --}}
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl p-4 mt-6">
                    <p class="text-sm">
                        Your subscription has been <strong>canceled</strong> but you still have access until <strong>{{ auth()->user()->subscription('default')->ends_at->format('M d, Y') }}</strong>. 
                        You can renew anytime to regain access.
                    </p>
                    <a 
                        href="{{ route('subscription.page') }}" 
                        class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700"
                    >
                        View Subscription Plans
                    </a>
                </div>
            @elseif ($latestSubscription && $latestSubscription->stripe_status === 'canceled')
                {{-- Subscription canceled: show renewal link --}}
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl p-4 mt-6">
                    <p class="text-sm">
                        Your subscription has been <strong>canceled</strong>. 
                        You can renew anytime to regain access.
                    </p>
                    <a 
                        href="{{ route('subscription.page') }}" 
                        class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700"
                    >
                        View Subscription Plans
                    </a>
                </div>
            @else
                {{-- No subscription at all --}}
                <div class="bg-gray-50 border border-gray-200 text-gray-700 rounded-xl p-4 mt-6">
                    <p class="text-sm">
                        You don't have an active subscription.
                    </p>
                    <a 
                        href="{{ route('subscription.page') }}" 
                        class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700"
                    >
                        Subscribe Now
                    </a>
                </div>
            @endif
            </div>
            @else
                <p class="text-sm text-gray-500 mt-6">No active subscription found.</p>
            @endif


    {{-- Transactions --}}
    <div class="mt-10 bg-white p-6 rounded-2xl shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Subscription</h3>

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
                            $symbols = ['NGN' => '₦', 'USD' => '$', 'EUR' => '€', 'GBP' => '£'];
                            $symbol = $symbols[$txn->currency] ?? '';
                            $user = auth()->user();

                            if ($txn->interval === 'month') {
                                $planName = $user->is_premium
                                    ? 'Monthly Premium Plan'
                                    : 'Monthly Standard Plan';
                            } elseif ($txn->interval === 'year') {
                                $planName = $user->is_premium
                                    ? 'Yearly Premium Plan'
                                    : 'Yearly Standard Plan';
                            } else {
                                $planName = 'Standard Plan';
                            }
                        @endphp

                        <tr>
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800">{{ $planName }}</td>
                            <td class="px-4 py-2">
                                {{ $symbol }}{{ number_format($txn->amount, 2) }} / {{ $txn->interval }}
                            </td>
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($txn->starts_at)->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium 
                                    {{ $txn->stripe_status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($txn->stripe_status) }}
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
