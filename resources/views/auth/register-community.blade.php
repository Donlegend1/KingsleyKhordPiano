@extends('layouts.app')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-gradient-to-br from-white via-slate-50 to-slate-100 px-4 py-16">
  <div class="max-w-lg w-full">
    <!-- Header -->
    <div class="text-center mb-8">
      <h1 class="text-4xl font-black text-slate-900 mb-1 tracking-tight">Join KingsleyKhord Piano</h1>
      <p class="text-slate-500 text-sm">Create your account and unlock premium content</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
      <div class="p-8">
        <form method="POST" action="{{ route('community.register.submit') }}" class="space-y-5">
          @csrf

          <!-- Name Row -->
          <div class="grid md:grid-cols-2 gap-4">
            <!-- First Name -->
            <div>
              <label for="first_name" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wide">
                {{ __('First Name') }}
              </label>
              <input
                type="text"
                name="first_name"
                id="first_name"
                value="{{ old('first_name') }}"
                placeholder="John"
                class="w-full border border-slate-300 px-3.5 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm @error('first_name') border-red-500 @enderror"
              />
              @error('first_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Last Name -->
            <div>
              <label for="last_name" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wide">
                {{ __('Last Name') }}
              </label>
              <input
                type="text"
                name="last_name"
                id="last_name"
                value="{{ old('last_name') }}"
                placeholder="Doe"
                class="w-full border border-slate-300 px-3.5 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm @error('last_name') border-red-500 @enderror"
              />
              @error('last_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wide">
              {{ __('Email Address') }}
            </label>
            <input
              type="email"
              name="email"
              id="email"
              value="{{ old('email') }}"
              placeholder="you@example.com"
              class="w-full border border-slate-300 px-3.5 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm @error('email') border-red-500 @enderror"
            />
            @error('email')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Password -->
          <div x-data="{ show: false }" class="relative">
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wide">
              {{ __('Password') }}
            </label>
            <div class="relative">
              <input
                :type="show ? 'text' : 'password'"
                name="password"
                id="password"
                placeholder="Min 4 characters"
                autocomplete="new-password"
                class="w-full border border-slate-300 px-3.5 py-2 pr-10 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm @error('password') border-red-500 @enderror"
              />
              <button
                type="button"
                class="absolute right-3 top-3 text-slate-500 hover:text-slate-700 transition text-sm"
                @click="show = !show"
              >
                <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
            @error('password')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Confirm Password -->
          <div x-data="{ show: false }" class="relative">
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5 uppercase tracking-wide">
              {{ __('Confirm Password') }}
            </label>
            <div class="relative">
              <input
                :type="show ? 'text' : 'password'"
                name="password_confirmation"
                id="password_confirmation"
                placeholder="Confirm password"
                autocomplete="new-password"
                class="w-full border border-slate-300 px-3.5 py-2 pr-10 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
              />
              <button
                type="button"
                class="absolute right-3 top-3 text-slate-500 hover:text-slate-700 transition text-sm"
                @click="show = !show"
              >
                <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
          </div>

          <!-- reCAPTCHA -->
          <div class="pt-1">
            {!! NoCaptcha::display() !!}
            @error('g-recaptcha-response')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Terms Checkbox -->
          <label class="flex items-start text-xs text-slate-600 cursor-pointer group">
            <input
              type="checkbox"
              required
              class="w-4 h-4 mt-0.5 bg-white border border-slate-300 rounded accent-blue-600 group-hover:border-blue-400 transition"
            />
            <span class="ml-2.5">
              I agree to the <a href="#" class="text-blue-600 font-semibold hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 font-semibold hover:underline">Privacy Policy</a>
            </span>
          </label>

          <!-- Submit Button -->
          <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-all active:scale-95 mt-6 text-sm"
          >
            {{ __('Create Account') }}
          </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-300"></div>
          </div>
          <div class="relative flex justify-center text-xs">
            <span class="px-2 bg-white text-slate-500">Already registered?</span>
          </div>
        </div>

        <!-- Login Link -->
        <a href="{{ route('login-community') }}" class="block w-full bg-slate-100 hover:bg-slate-200 text-slate-900 font-semibold py-2.5 rounded-lg text-center transition text-sm">
          {{ __('Sign in instead') }}
        </a>
      </div>
    </div>

    <!-- Info -->
    <p class="text-center text-slate-500 text-xs mt-6">
      By creating an account, you agree to receive updates and marketing communications from us.
    </p>
  </div>
</section>
@endsection
