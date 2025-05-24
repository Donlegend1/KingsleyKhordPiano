@extends('layouts.app')

@section('content')

<section x-data="registerForm()" class="container mx-auto px-4 py-12">
  <div class="flex flex-col md:flex-row items-stretch gap-8">
    <div class="w-full md:w-3/5 bg-white p-6 rounded-lg shadow-lg">

      <form action="/stripe/create" method="post">
        @csrf
        <input type="hidden" name="plan" value="{{ request()->query('plan') }}">
        <button type="submit">
          submit
        </button>
      </form>
      <h2 class="text-3xl font-bold text-gray-800 mb-6">{{ __('Register') }}</h2>
      <form @submit.prevent="registerUser" class="space-y-4 h-full">
        @csrf

        <!-- Name -->
        <div>
          <label class="block text-gray-700 mb-1" for="name">{{ __('Name') }}</label>
          <input x-model="name" name="name" id="name" type="text" placeholder="Your Name" value="{{ old('name') }}"
            class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200 @error('name') border-red-500 @enderror">
          @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>


        <!-- Email -->
        <div>
          <label class="block text-gray-700 mb-1" for="email">{{ __('Email') }}</label>
          <input x-model="email" name="email" id="email" type="email" placeholder="Your Email" value="{{ old('email') }}"
            class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200 @error('email') border-red-500 @enderror">
          @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div x-data="{ show: false }" class="relative">
          <label class="block text-gray-700 mb-1" for="password">{{ __('Password') }}</label>
          <input x-model="password" :type="show ? 'text' : 'password'" name="password" id="password"
            class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200 pr-10 @error('password') border-red-500 @enderror"
            placeholder="Your Password" autocomplete="new-password">
          <button type="button" class="absolute right-3 top-10 text-gray-500" @click="show = !show">
            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </button>
          @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Confirm Password -->
        <div x-data="{ show: false }" class="relative">
          <label class="block text-gray-700 mb-1" for="password_confirmation">{{ __('Confirm Password') }}</label>
          <input x-model="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
            class="w-full border border-gray-300 p-3 rounded focus:ring focus:ring-blue-200 pr-10"
            placeholder="Confirm Password">
          <button type="button" class="absolute right-3 top-10 text-gray-500" @click="show = !show">
            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
          </button>
        </div>


        <!-- Submit Button -->
        <button type="submit"
          class="bg-[#FFD736] hover:bg-[#a7923e] text-black font-semibold py-3 px-6 rounded transition-colors">
          {{ __('Continue') }} <i class="fa fa-paper-plane ml-2" aria-hidden="true"></i>
        </button>
      </form>
    </div>
    <!-- Image (Right Side) -->
    <div class="w-full md:w-2/5 hidden md:block">
      <img src="/images/banner.png" alt="Register" class="w-full h-full object-cover rounded-lg shadow-lg">
    </div>
  </div>

  <!-- Payment Modal -->
  <div x-show="showPaymentModal" x-cloak class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center">
  <div class="bg-[#2D2D2D] rounded-2xl shadow-2xl px-8 py-10 w-full max-w-md mx-auto text-white space-y-6">

    <!-- Heading -->
    <div class="text-center">
      <h2 class="text-2xl font-bold mb-1">Choose Your Payment Option</h2>
      <p class="text-sm text-gray-300">Select your preferred method to complete your payment</p>
    </div>

    <!-- Buttons Section -->
    <div class="flex flex-col space-y-4">
      <!-- Paystack Button -->
      <form action="/paystack" method="POST">
       @csrf
       <input type="hidden" name="plan" value="{{ request()->query('plan') }}">
        <button type="submit" class="w-full bg-white text-black hover:bg-gray-200 transition-all duration-200 font-semibold py-3 px-4 rounded-lg flex items-center justify-center gap-3 shadow">
          <img src="/icons/paystack.png" alt="Paystack" class="w-6 h-6" />
          Pay with Paystack
        </button>
      </form>

      <!-- Stripe Button -->
      <form action="/stripe/create" method="POST" >
          @csrf
          <input type="hidden" name="plan" value="{{ request()->query('plan') }}">
        <button type="submit" class="w-full bg-[#FFD736] text-black hover:bg-yellow-400 transition-all duration-200 font-semibold py-3 px-4 rounded-lg flex items-center justify-center gap-3 shadow">
          <img src="/icons/stripe.png" alt="Stripe" class="w-6 h-6" />
          Pay with Stripe
        </button>
      </form>
       <form action="paypal/create-order" method="POST" >
          @csrf
          <input type="hidden" name="plan" value="{{ request()->query('plan') }}">
        <button type="submit" class="w-full bg-[#FFD736] text-black hover:bg-yellow-400 transition-all duration-200 font-semibold py-3 px-4 rounded-lg flex items-center justify-center gap-3 shadow">
          <img src="/icons/paypal.png" alt="Stripe" class="w-6 h-6" />
          Pay with Paypal
        </button>
      </form>
    </div>

    <!-- Footer -->
    <div class="text-center text-gray-400 text-xs pt-4 border-t border-gray-600">
      <p class="flex items-center justify-center gap-2">
        Powered by
        <img src="/icons/stripe2.png" alt="Stripe" class="h-4">
        <img src="/icons/paystack2.png" alt="Paystack" class="h-4">
      </p>
    </div>
    
  </div>
</div>


</section>


@endsection
