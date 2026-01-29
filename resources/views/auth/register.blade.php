@extends('layouts.app')

@section('content')
<section class="min-h-screen flex items-center justify-center px-4 py-16
                bg-gradient-to-br from-slate-50 via-white to-slate-100">
  <div class="max-w-lg w-full">

    <!-- Header -->
    <div class="text-center mb-8">
      <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
        Join KingsleyKhord
      </h1>
      <p class="text-gray-500 text-sm mt-1">
        Create your account and unlock premium content
      </p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
      <div class="p-8">
        <form method="POST" action="{{ route('community.register.submit') }}" class="space-y-5">
          @csrf

          <!-- Name -->
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                First Name
              </label>
              <input
                type="text"
                name="first_name"
                value="{{ old('first_name') }}"
                placeholder="John"
                class="w-full bg-white text-gray-900 border border-gray-300 px-4 py-3 rounded-lg
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition
                       @error('first_name') border-red-500 @enderror"
              />
              @error('first_name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Last Name
              </label>
              <input
                type="text"
                name="last_name"
                value="{{ old('last_name') }}"
                placeholder="Doe"
                class="w-full bg-white text-gray-900 border border-gray-300 px-4 py-3 rounded-lg
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition
                       @error('last_name') border-red-500 @enderror"
              />
              @error('last_name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Email Address
            </label>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="you@example.com"
              class="w-full bg-white text-gray-900 border border-gray-300 px-4 py-3 rounded-lg
                     focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition
                     @error('email') border-red-500 @enderror"
            />
            @error('email')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
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
                placeholder="Minimum 8 characters"
                autocomplete="new-password"
                class="w-full bg-white text-gray-900 border border-gray-300 px-4 py-3 pr-11 rounded-lg
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition
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
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Confirm Password -->
          <div x-data="{ show: false }">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Confirm Password
            </label>
            <div class="relative">
              <input
                :type="show ? 'text' : 'password'"
                name="password_confirmation"
                placeholder="Confirm password"
                autocomplete="new-password"
                class="w-full bg-white text-gray-900 border border-gray-300 px-4 py-3 pr-11 rounded-lg
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
              />
              <button
                type="button"
                @click="show = !show"
                class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700"
              >
                <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
              </button>
            </div>
          </div>

          <!-- reCAPTCHA -->
          <div>
            {!! NoCaptcha::display() !!}
            @error('g-recaptcha-response')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Terms -->
          <label class="flex items-start text-sm text-gray-600">
            <input
              type="checkbox"
              required
              class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="ml-2">
              I agree to the
              <a href="#" class="text-blue-600 font-semibold hover:underline">Terms</a>
              and
              <a href="#" class="text-blue-600 font-semibold hover:underline">Privacy Policy</a>
            </span>
          </label>

          <!-- Submit -->
          <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg
                   shadow-md transition active:scale-95 mt-4"
          >
            Create Account
          </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="bg-white px-3 text-gray-500">Already have an account?</span>
          </div>
        </div>

        <!-- Login -->
        <a
          href="{{ route('login-community') }}"
          class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-3 rounded-lg
                 text-center transition"
        >
          Sign in instead
        </a>
      </div>
    </div>

    <p class="text-center text-xs text-gray-500 mt-6">
      By signing up, you agree to receive updates from KingsleyKhord.
    </p>
  </div>
</section>
@endsection
