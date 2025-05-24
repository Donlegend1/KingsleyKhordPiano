@extends('layouts.app')
@section('content')
<section class="py-5 flex items-center justify-center bg-gray-100 px-4" style="background-image: url('/images/banner.png')">
  <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">{{ __('Welcome Back') }}</h2>
    
    <form class="space-y-5" method="POST" action="{{ route('login') }}">
    @csrf
      <div>
        <label for="email" class="block text-gray-700 mb-1">{{ __('Email Address') }}</label>
        <input
          type="email"
          id="email"
          placeholder="you@example.com"
          name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>

     <div x-data="{ show: false, password: '' }" class="relative">
        <label class="block text-gray-700 mb-1" for="password">Password</label>
        <input 
          x-model="password" 
          :type="show ? 'text' : 'password'" 
          name="password" 
          id="password"
          class="w-full border border-gray-300 p-3 rounded-lg focus:ring focus:ring-blue-200 pr-10 @error('password') border-red-500 @enderror"
          placeholder="........" 
          autocomplete="new-password"
        >
        
        <button 
          type="button" 
          class="absolute right-3 top-[2.5rem] text-gray-500 focus:outline-none"
          @click="show = !show"
        >
          <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
        </button>

        @error('password')
          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex items-center justify-between text-sm text-gray-600">
        <label class="flex items-center gap-2">
          <input type="checkbox" class="text-black focus:ring-2 focus:ring-blue-500" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}/>
          {{ __('Remember Me') }}
        </label>
        @if (Route::has('password.request'))
            <a class="btn btn-link" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        @endif
      </div>
      <button
        type="submit"
        class="w-full bg-[#FFD736] hover:bg-[#a7923e] text-white font-semibold py-3 rounded-md transition duration-200"
      >
      {{ __('Login') }}
      </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
      Don't have an account?
      <a href="/plans" class="text-blue-500 hover:underline">Sign up</a>
    </div>
  </div>
</section>

@endsection
