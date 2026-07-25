@extends('layouts.app')

@section('content')

<section class="py-5 h-[78vh] flex items-center justify-center px-4 bg-white">
  <div class="max-w-lg w-full bg-white p-10 rounded-2xl border border-gray-200 text-center">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Lost your password?') }}</h2>
    <p class="text-gray-600 mb-6">
        {{ __('Please enter your email address. You will receive a link to create a new password via email.') }}
    </p>

    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-left">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="text-left">
        <input
          type="email"
          id="email"
          placeholder="Email"
          name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#E14434]"
        />
        @error('email')
            <span class="invalid-feedback text-red-600 text-sm" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>

      <div class="mt-6 flex justify-center">
        <button
            type="submit"
            class="px-8 py-3 bg-[#E14434] hover:bg-[#c9382a] text-white font-bold text-sm uppercase tracking-wide rounded-md transition duration-200"
          >
          {{ __('Reset Password') }}
          </button>
      </div>
    </form>
  </div>
</section>
@endsection
