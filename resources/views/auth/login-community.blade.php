@extends('layouts.app')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-black px-4">
    <div class="w-full max-w-md">
        <!-- Brand -->
        <div class="text-center mb-10">
            <h1 class="text-5xl font-extrabold text-black tracking-tight">
                Kingsley<span class="text-yellow-500">Khord</span>
            </h1>
            <p class="mt-2 text-xs tracking-widest uppercase text-slate-400">
                Piano Community
            </p>
        </div>

        <!-- Card -->
        <div class="relative bg-slate-900/70 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl p-8">
            <form method="POST" action="{{ route('community.login.submit') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-xs font-medium text-slate-400 mb-1">
                        Email address
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                    />
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div x-data="{ show: false }">
                    <label class="block text-xs font-medium text-slate-400 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            placeholder="••••••••"
                            class="w-full rounded-xl bg-slate-950 border border-slate-800 px-4 py-3 pr-11 text-sm text-black placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                        />
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-slate-300 transition"
                        >
                            <i :class="show ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Options -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-400 hover:text-slate-300 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded bg-slate-800 border-slate-700 accent-blue-500"
                            {{ old('remember') ? 'checked' : '' }}
                        />
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-blue-400 hover:text-blue-300 font-medium transition">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full mt-2 bg-gradient-to-r from-black to-yellow-500 hover:from-blue-500 hover:to-blue-400 text-black font-semibold py-3 rounded-xl transition-all active:scale-[0.98] shadow-lg shadow-blue-500/20"
                >
                    Sign in
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-800"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-slate-900 text-slate-500">
                        New to the community?
                    </span>
                </div>
            </div>

            <!-- Register -->
            <a href="{{ route('register-community') }}"
               class="block w-full text-center py-3 rounded-xl border border-slate-800 text-slate-300 hover:bg-slate-800 hover:text-white transition font-medium">
                Create an account
            </a>
        </div>

        <!-- Footer -->
        <p class="mt-6 text-center text-xs text-slate-500">
            © {{ date('Y') }} KingsleyKhord. Built for builders.
        </p>
    </div>
</section>
@endsection
