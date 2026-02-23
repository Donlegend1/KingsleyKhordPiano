@extends('layouts.app')

@section('content')
<section class="relative min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-[#0B0B0B] via-[#FFF] to-black">

  <!-- Soft ambient glow -->
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,215,54,0.08),transparent_60%)]"></div>

  <!-- Card -->
  <div
    class="relative z-10 w-full max-w-md
           bg-white/90 backdrop-blur-xl
           rounded-3xl px-8 py-10
           shadow-[0_25px_60px_-15px_rgba(0,0,0,0.35)]"
  >
    <!-- Header -->
    <div class="text-center mb-8">
      <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
        Welcome Back
      </h2>
      
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
      @csrf

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Email Address
        </label>
        <input
          type="email"
          name="email"
          value="{{ old('email') }}"
          required
          autofocus
          placeholder="you@example.com"
          class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3
                 text-gray-900 placeholder-gray-400
                 focus:ring-2 focus:ring-[#FFD736] focus:border-[#FFD736]
                 transition @error('email') border-red-500 @enderror"
        />
        @error('email')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div x-data="{ show: false }">
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Password
        </label>

        <div class="relative">
          <input
            :type="show ? 'text' : 'password'"
            name="password"
            placeholder="••••••••"
            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pr-12
                   text-gray-900 placeholder-gray-400
                   focus:ring-2 focus:ring-[#FFD736] focus:border-[#FFD736]
                   transition @error('password') border-red-500 @enderror"
          />

          <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-4 flex items-center text-gray-500 hover:text-gray-800"
          >
            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </button>
        </div>

        @error('password')
          <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Remember / Forgot -->
      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-gray-600">
          <input
            type="checkbox"
            name="remember"
            class="rounded border-gray-300 text-[#FFD736] focus:ring-[#FFD736]"
            {{ old('remember') ? 'checked' : '' }}
          />
          Remember me
        </label>

        @if (Route::has('password.request'))
          <a
            href="{{ route('password.request') }}"
            class="font-medium text-[#9C7A1C] hover:underline"
          >
            Forgot password?
          </a>
        @endif
      </div>

      <!-- CTA -->
      <button
        type="submit"
        class="w-full py-3 rounded-xl font-semibold text-black
               bg-gradient-to-r from-[#FFD736] to-[#E6C531]
               hover:from-[#E6C531] hover:to-[#FFD736]
               shadow-lg transition active:scale-[0.98]"
      >
        Login
      </button>
    </form>

    <!-- Footer -->
    <div class="mt-8 text-center text-sm text-gray-600">
      Don’t have an account?
      <a href="/plans" class="font-semibold text-[#9C7A1C] hover:underline">
        Sign up
      </a>
    </div>
  </div>
</section>
@endsection
