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

      <div x-data="{ showPassword: false }" class="relative">
        <label for="password" class="block text-gray-700 mb-1">{{ __('Password') }}</label>
        
        <input
            :type="showPassword ? 'text' : 'password'"
            id="password"
            name="password"
            required
            autocomplete="current-password"
            placeholder="••••••••"
            class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
    
        <!-- Toggle Visibility Button -->
        <button 
        type="button" 
        @click="showPassword = !showPassword" 
        class="absolute right-3 top-12 transform -translate-y-1/2 focus:outline-none"
    >
        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-500"></i>
    </button>
    
        @error('password')
            <span class="invalid-feedback text-red-600" role="alert">
                <strong>{{ $message }}</strong>
            </span>
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
