@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 px-4 py-12">
    <div class="w-full max-w-md bg-white shadow-md rounded-xl p-8 space-y-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-800">Verify Your Email</h2>
            <p class="mt-2 text-sm text-gray-600">
                Thanks for signing up! Please verify your email address by clicking the link we just sent to your inbox.
            </p>
        </div>

        {{-- ✅ Success message for resend --}}
        @if (session('status') == 'verification-link-sent')
            <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-md text-sm text-center animate-fade-in">
                ✅ A new verification link has been sent to your email address.
            </div>
        @endif

        {{-- ✅ Display any other session messages if needed --}}
        @if (session('message'))
            <div class="bg-blue-100 border border-blue-200 text-blue-800 px-4 py-3 rounded-md text-sm text-center animate-fade-in">
                {{ session('message') }}
            </div>
        @endif

        {{-- 📤 Resend Button --}}
        <form method="POST" action="{{ route('verification.resend') }}" class="space-y-4">
            @csrf
            <div class="text-center">
                <button type="submit"
                    class="inline-flex justify-center px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition">
                    Resend Verification Email
                </button>
            </div>
        </form>

        {{-- 🔐 Logout Button --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="text-center">
                <button type="submit"
                    class="inline-flex justify-center px-6 py-2 text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-md transition">
                    Log Out
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
