@extends('layouts.app')

@section('content')
<section class="container mx-auto px-4 py-12 min-h-screen">
  <div class="flex flex-col md:flex-row items-stretch gap-8">
    
    <!-- Registration Form -->
    <div class="w-full md:w-3/5 bg-white p-6 rounded-lg shadow-lg">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">{{ __('Register') }}</h2>

      <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- First Name -->
        <div>
          <label for="first_name" class="block text-gray-700 mb-1">{{ __('First Name') }}</label>
          <input name="first_name" id="first_name" type="text" value="{{ old('first_name') }}"
            placeholder="First name"
            class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200 @error('first_name') border-red-500 @enderror">
          @error('first_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Last Name -->
        <div>
          <label for="last_name" class="block text-gray-700 mb-1">{{ __('Last Name') }}</label>
          <input name="last_name" id="last_name" type="text" value="{{ old('last_name') }}"
            placeholder="Last name"
            class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200 @error('last_name') border-red-500 @enderror">
          @error('last_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-gray-700 mb-1">{{ __('Email') }}</label>
          <input name="email" id="email" type="email" value="{{ old('email') }}"
            placeholder="Your Email"
            class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200 @error('email') border-red-500 @enderror">
          @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div x-data="{ show: false }" class="relative">
          <label for="password" class="block text-gray-700 mb-1">{{ __('Password') }}</label>
          <input name="password" id="password" :type="show ? 'text' : 'password'" autocomplete="new-password"
            placeholder="Your Password"
            class="w-full border border-gray-300 p-3 rounded pr-10 focus:ring focus:ring-blue-200 @error('password') border-red-500 @enderror">
          <button type="button" class="absolute right-3 top-10 text-gray-500" @click="show = !show">
            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </button>
          @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Confirm Password -->
        <div x-data="{ show: false }" class="relative">
          <label for="password_confirmation" class="block text-gray-700 mb-1">{{ __('Confirm Password') }}</label>
          <input name="password_confirmation" id="password_confirmation" :type="show ? 'text' : 'password'"
            placeholder="Confirm Password"
            class="w-full border border-gray-300 p-3 rounded pr-10 focus:ring focus:ring-blue-200">
          <button type="button" class="absolute right-3 top-10 text-gray-500" @click="show = !show">
            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </button>
        </div>

        <!-- reCAPTCHA -->
        <div>
          {!! NoCaptcha::display() !!}
          @error('g-recaptcha-response')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit"
          class="bg-[#FFD736] hover:bg-[#a7923e] text-black font-semibold py-3 px-6 rounded transition-colors w-full">
          {{ __('Continue') }} <i class="fa fa-paper-plane ml-2"></i>
        </button>
      </form>
    </div>

    <!-- Image -->
    <div class="w-full md:w-2/5 hidden md:block">
      <img src="/images/banner.png" alt="Register" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
  </div>
</section>

@endsection
