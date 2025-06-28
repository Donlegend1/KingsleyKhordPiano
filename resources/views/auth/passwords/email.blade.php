@extends('layouts.app')

@section('content')

<section class="py-5 h-[78vh] flex items-center justify-center bg-gray-100 px-4" style="background-image: url('/images/banner.png')">
  <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">{{ __('Reset Password') }}</h2>
    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('status') }}
        </div>
    @endif
    
   <form method="POST" action="{{ route('password.email') }}">
                        @csrf
      <div class="">
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

      <div class="my-5">
    <button
        type="submit"
        class="w-full bg-[#FFD736] hover:bg-[#a7923e] text-white font-semibold py-3 rounded-md transition duration-200"
      >
      {{ __('Send Password Reset Link') }}
      </button>
      </div>

     
    </form>

    
  </div>
</section>
@endsection
