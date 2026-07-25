@extends('layouts.app')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-gray-50 px-4 pt-24 pb-16">
  <div class="max-w-2xl w-full bg-white p-8 sm:p-12 rounded-2xl border border-gray-100">
    <div class="text-center mb-8">
      <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
        {{ __('Create Your Account') }}
      </h2>
      <p class="mt-2 text-sm text-gray-500 leading-relaxed">
        A few details and you're in — let's get your piano journey started.
      </p>
    </div>

    <form method="POST" action="{{ route('register') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="space-y-4">
      @csrf

      <!-- First Name -->
      <div>
        <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
          {{ __('First Name') }}
        </label>
        <input
          type="text"
          name="first_name"
          id="first_name"
          value="{{ old('first_name') }}"
          placeholder="First name"
          class="w-full border border-gray-200 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition @error('first_name') border-red-500 @enderror"
        />
        @error('first_name')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Last Name -->
      <div>
        <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
          {{ __('Last Name') }}
        </label>
        <input
          type="text"
          name="last_name"
          id="last_name"
          value="{{ old('last_name') }}"
          placeholder="Last name"
          class="w-full border border-gray-200 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition @error('last_name') border-red-500 @enderror"
        />
        @error('last_name')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
          {{ __('Email Address') }}
        </label>
        <input
          type="email"
          name="email"
          id="email"
          value="{{ old('email') }}"
          placeholder="you@example.com"
          class="w-full border border-gray-200 px-4 py-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div x-data="{ show: false }" class="relative">
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
          {{ __('Password') }}
        </label>
        <input
          :type="show ? 'text' : 'password'"
          name="password"
          id="password"
          placeholder="Your password"
          autocomplete="new-password"
          class="w-full border border-gray-200 px-4 py-2.5 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition @error('password') border-red-500 @enderror"
        />
        <button
          type="button"
          class="absolute right-3.5 top-[2.55rem] text-gray-400 hover:text-gray-600"
          @click="show = !show"
        >
          <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'" class="text-sm"></i>
        </button>
        @error('password')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div x-data="{ show: false }" class="relative">
        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
          {{ __('Confirm Password') }}
        </label>
        <input
          :type="show ? 'text' : 'password'"
          name="password_confirmation"
          id="password_confirmation"
          placeholder="Confirm password"
          autocomplete="new-password"
          class="w-full border border-gray-200 px-4 py-2.5 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900 transition"
        />
        <button
          type="button"
          class="absolute right-3.5 top-[2.55rem] text-gray-400 hover:text-gray-600"
          @click="show = !show"
        >
          <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'" class="text-sm"></i>
        </button>
      </div>

      <!-- reCAPTCHA -->
      <div class="pt-1">
        {!! NoCaptcha::display() !!}
        @error('g-recaptcha-response')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition-colors mt-2"
      >
        {{ __('Continue') }} <i class="fa fa-paper-plane text-sm"></i>
      </button>
    </form>

    <!-- Login Link -->
    <p class="mt-6 text-center text-sm text-gray-500">
      Already have an account?
      <a href="{{ route('login') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="text-gray-900 font-semibold hover:underline">
        {{ __('Login here') }}
      </a>
    </p>
  </div>
</section>
@endsection
