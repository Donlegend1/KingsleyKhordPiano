@extends('layouts.app')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-gray-50 px-4 pt-24 pb-16">

  <!-- Card -->
  <div class="w-full max-w-2xl bg-white p-8 sm:p-12 rounded-2xl border border-gray-100">

    <!-- Header -->
    <div class="text-center mb-8">
      <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
        Welcome Back
      </h2>
      <p class="mt-2 text-sm text-gray-500 leading-relaxed">
        Sign in to pick up right where you left off.
      </p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf

      <!-- Email -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          Email Address
        </label>
        <input
          type="email"
          name="email"
          value="{{ old('email') }}"
          required
          autofocus
          placeholder="you@example.com"
          class="w-full border border-gray-200 px-4 py-2.5 rounded-lg
                 text-gray-900 placeholder-gray-400
                 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900
                 transition @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div x-data="{ show: false }" class="relative">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          Password
        </label>
        <input
          :type="show ? 'text' : 'password'"
          name="password"
          placeholder="Your password"
          class="w-full border border-gray-200 px-4 py-2.5 pr-10 rounded-lg
                 text-gray-900 placeholder-gray-400
                 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900
                 transition @error('password') border-red-500 @enderror"
        />
        <button
          type="button"
          @click="show = !show"
          class="absolute right-3.5 top-[2.55rem] text-gray-400 hover:text-gray-600"
        >
          <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'" class="text-sm"></i>
        </button>
        @error('password')
          <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Remember / Forgot -->
      <div class="flex items-center justify-between text-sm pt-1">
        <label class="flex items-center gap-2 text-gray-600">
          <input
            type="checkbox"
            name="remember"
            class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
            {{ old('remember') ? 'checked' : '' }}
          />
          Remember me
        </label>

        @if (Route::has('password.request'))
          <a
            href="{{ route('password.request') }}"
            class="font-semibold text-gray-900 hover:underline"
          >
            Forgot password?
          </a>
        @endif
      </div>

      <!-- CTA -->
      <button
        type="submit"
        class="w-full py-3 rounded-lg font-semibold text-white bg-gray-900 hover:bg-gray-800 transition-colors mt-2"
      >
        Login
      </button>
    </form>

    <!-- Footer -->
    <p class="mt-6 text-center text-sm text-gray-500">
      Don't have an account?
      <a href="/plans" class="font-semibold text-gray-900 hover:underline">
        Sign up
      </a>
    </p>
  </div>
</section>
@endsection
