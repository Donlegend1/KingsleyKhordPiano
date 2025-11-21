@extends('layouts.app')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-16"  style="background-image: url('/images/probanner.jpeg')">
  <div class="max-w-2xl w-full bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
    <h2 class="text-3xl md:text-4xl font-extrabold text-center text-gray-800 mb-6">
      {{ __('Create Your Account') }}
    </h2>
    <p class="text-center text-sm text-gray-500 mb-8">
      Join KingsleyKhord Music Academy and start your journey toward piano mastery today.
    </p>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
      @csrf

      <!-- First Name -->
      <div>
        <label for="first_name" class="block text-gray-700 mb-1 font-medium">
          {{ __('First Name') }}
        </label>
        <input
          type="text"
          name="first_name"
          id="first_name"
          value="{{ old('first_name') }}"
          placeholder="First name"
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('first_name') border-red-500 @enderror"
        />
        @error('first_name')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Last Name -->
      <div>
        <label for="last_name" class="block text-gray-700 mb-1 font-medium">
          {{ __('Last Name') }}
        </label>
        <input
          type="text"
          name="last_name"
          id="last_name"
          value="{{ old('last_name') }}"
          placeholder="Last name"
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('last_name') border-red-500 @enderror"
        />
        @error('last_name')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700 mb-1 font-medium">
          {{ __('Email Address') }}
        </label>
        <input
          type="email"
          name="email"
          id="email"
          value="{{ old('email') }}"
          placeholder="you@example.com"
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div x-data="{ show: false }" class="relative">
        <label for="password" class="block text-gray-700 mb-1 font-medium">
          {{ __('Password') }}
        </label>
        <input
          :type="show ? 'text' : 'password'"
          name="password"
          id="password"
          placeholder="Your Password"
          autocomplete="new-password"
          class="w-full border border-gray-300 p-3 rounded-lg pr-10 focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('password') border-red-500 @enderror"
        />
        <button
          type="button"
          class="absolute right-3 top-[2.6rem] text-gray-500 hover:text-gray-700"
          @click="show = !show"
        >
          <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
        </button>
        @error('password')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div x-data="{ show: false }" class="relative">
        <label for="password_confirmation" class="block text-gray-700 mb-1 font-medium">
          {{ __('Confirm Password') }}
        </label>
        <input
          :type="show ? 'text' : 'password'"
          name="password_confirmation"
          id="password_confirmation"
          placeholder="Confirm Password"
          autocomplete="new-password"
          class="w-full border border-gray-300 p-3 rounded-lg pr-10 focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition"
        />
        <button
          type="button"
          class="absolute right-3 top-[2.6rem] text-gray-500 hover:text-gray-700"
          @click="show = !show"
        >
          <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
        </button>
      </div>

      <!-- reCAPTCHA -->
      <div class="pt-2">
        {!! NoCaptcha::display() !!}
        @error('g-recaptcha-response')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full bg-[#FFD736] hover:bg-[#d6b937] text-black font-semibold py-3 rounded-lg shadow-md transition-transform transform hover:-translate-y-1"
      >
        {{ __('Continue') }} <i class="fa fa-paper-plane ml-2"></i>
      </button>
    </form>

    <!-- Login Link -->
    <div class="mt-6 text-center text-sm text-gray-600">
      Already have an account?
      <a href="{{ route('login') }}" class="text-yellow-600 font-semibold hover:underline">
        {{ __('Login here') }}
      </a>
    </div>
  </div>
</section>
@endsection
