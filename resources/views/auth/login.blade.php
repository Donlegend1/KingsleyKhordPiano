@extends('layouts.app')

@section('content')
<section
  class="min-h-screen flex items-center justify-center px-4 py-12 bg-cover bg-center relative"
  style="background-image: url('/images/probanner.jpeg')"
>
  <!-- Background overlay -->
  <div class="absolute inset-0 bg-black/50"></div>

  <div class="relative z-10 max-w-md w-full bg-white p-8 rounded-2xl shadow-2xl">
    <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-3">
      {{ __('Welcome Back') }}
    </h2>

    <p class="text-center text-sm text-gray-500 mb-6">
      We’ve migrated our system. If your old password doesn’t work,
      please reset it using “Forgot Password”.
    </p>

    <form class="space-y-5" method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('Email Address') }}
        </label>
        <input
          type="email"
          id="email"
          name="email"
          value="{{ old('email') }}"
          placeholder="you@example.com"
          required
          autofocus
          autocomplete="email"
          class="w-full bg-white text-gray-900 border border-gray-300 px-4 py-3 rounded-lg
                 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition
                 @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div x-data="{ show: false }">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('Password') }}
        </label>

        <div class="relative">
          <input
            :type="show ? 'text' : 'password'"
            id="password"
            name="password"
            placeholder="••••••••"
            autocomplete="current-password"
            class="w-full bg-white text-gray-900 border border-gray-300 px-4 py-3 pr-11 rounded-lg
                   focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition
                   @error('password') border-red-500 @enderror"
          />

          <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700"
          >
            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </button>
        </div>

        @error('password')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Remember + Forgot -->
      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-gray-600">
          <input
            type="checkbox"
            name="remember"
            class="rounded text-yellow-500 focus:ring-yellow-400"
            {{ old('remember') ? 'checked' : '' }}
          />
          {{ __('Remember Me') }}
        </label>

        @if (Route::has('password.request'))
          <a
            href="{{ route('password.request') }}"
            class="text-yellow-600 font-medium hover:underline"
          >
            {{ __('Forgot Password?') }}
          </a>
        @endif
      </div>

      <!-- Submit -->
      <button
        type="submit"
        class="w-full bg-[#FFD736] hover:bg-[#e6c531] text-black font-semibold py-3 rounded-lg
               shadow-md transition active:scale-95"
      >
        {{ __('Login') }}
      </button>
    </form>

    <!-- Register -->
    <div class="mt-6 text-center text-sm text-gray-600">
      Don’t have an account?
      <a href="/plans" class="text-yellow-600 font-semibold hover:underline">
        Sign up
      </a>
    </div>
  </div>
</section>
@endsection
