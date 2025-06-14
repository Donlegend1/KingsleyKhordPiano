@extends('layouts.app')

@section('content')
<section class="py-5 min-h-[78vh] flex items-center justify-center bg-gray-100 px-4" style="background-image: url('/images/banner.png')">
  <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md backdrop-blur-sm bg-opacity-90">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">{{ __('Reset Password') }}</h2>
    
    <form method="POST" action="{{ route('password.update') }}">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">

      <!-- Email -->
      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('Email Address') }}
        </label>
        <input
          type="email"
          id="email"
          name="email"
          value="{{ $email ?? old('email') }}"
          required
          autocomplete="email"
          autofocus
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400 @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('Password') }}
        </label>
        <input
          id="password"
          type="password"
          name="password"
          required
          autocomplete="new-password"
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400 @error('password') border-red-500 @enderror"
        />
        @error('password')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div class="mb-6">
        <label for="password-confirm" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('Confirm Password') }}
        </label>
        <input
          id="password-confirm"
          type="password"
          name="password_confirmation"
          required
          autocomplete="new-password"
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400"
        />
      </div>

      <!-- Submit Button -->
      <div class="mt-4">
        <button
          type="submit"
          class="w-full bg-[#FFD736] hover:bg-[#a7923e] text-black font-semibold py-3 rounded-md transition duration-200"
        >
          {{ __('Reset Password') }}
        </button>
      </div>
    </form>
  </div>
</section>
@endsection
