@extends('layouts.app')
@section('content')
<section class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12"  style="background-image: url('/images/probanner.jpeg')">
  <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
    <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-6">
      {{ __('Welcome Back') }}
    </h2>

    <p class="text-center text-xs text-gray-500 mb-6">
      We’ve recently migrated our system. If your old password doesn’t work, please use the “Forgot Password” option to reset it.
    </p>

    <form class="space-y-5" method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700 mb-1 font-medium">
          {{ __('Email Address') }}
        </label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="you@example.com"
          value="{{ old('email') }}"
          required
          autocomplete="email"
          autofocus
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div x-data="{ show: false }" class="relative">
        <label for="password" class="block text-gray-700 mb-1 font-medium">
          Password
        </label>
        <input
          :type="show ? 'text' : 'password'"
          name="password"
          id="password"
          placeholder="••••••••"
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-yellow-300 focus:border-yellow-400 transition pr-10 @error('password') border-red-500 @enderror"
          autocomplete="current-password"
        />
        <button
          type="button"
          class="absolute right-3 top-[2.6rem] text-gray-500 hover:text-gray-700"
          @click="show = !show"
        >
          <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
        </button>
        @error('password')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Remember Me + Forgot Password -->
      <div class="flex items-center justify-between text-sm text-gray-600">
        <label class="flex items-center gap-2">
          <input
            type="checkbox"
            class="rounded text-yellow-500 focus:ring-yellow-400"
            name="remember"
            id="remember"
            {{ old('remember') ? 'checked' : '' }}
          />
          {{ __('Remember Me') }}
        </label>

        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-yellow-600 hover:underline font-medium">
            {{ __('Forgot Password?') }}
          </a>
        @endif
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full bg-[#FFD736] hover:bg-[#d6b937] text-black font-semibold py-3 rounded-lg shadow-md transition-transform transform hover:-translate-y-1"
      >
        {{ __('Login') }}
      </button>
    </form>

    <!-- Sign up link -->
    <div class="mt-6 text-center text-sm text-gray-600">
      Don’t have an account?
      <a href="/plans" class="text-yellow-600 font-semibold hover:underline">Sign up</a>
    </div>
  </div>
</section>
@endsection



