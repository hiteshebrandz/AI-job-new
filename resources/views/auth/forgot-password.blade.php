@extends('layouts.app')

@section('title', 'Forgot Password')

@section('body-class', 'bg-[#0F172A] text-[#E2E8F0] min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<main class="flex w-full min-h-screen">
    @include('auth._panel')

    <section class="w-full lg:w-[45%] flex items-center justify-center p-8 bg-[#0F172A]">
        <div class="w-full max-w-[420px] animate-fade-in">

            <div class="mb-8">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[13px] text-[#64748B] hover:text-[#C4B5FD] transition-colors mb-6">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    Back to Login
                </a>
                <h2 class="text-[28px] font-bold text-[#E2E8F0] mb-2">Forgot Password?</h2>
                <p class="text-[14px] text-[#64748B]">Enter your email address and we'll send you a reset link.</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-900/40 border border-emerald-500/30 text-emerald-400 text-[14px] flex items-center gap-3">
                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">check_circle</span>
                    {{ session('status') }}
                </div>
            @endif

            <div class="glass-card p-8">
                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-[12px] font-semibold uppercase tracking-wider text-[#64748B] mb-2">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="input-dark"
                            placeholder="you@company.com"
                        >
                        @error('email')
                            <p class="mt-1.5 text-[12px] text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full py-3">
                        Send Reset Link
                    </button>

                    <p class="text-center text-[13px] text-[#64748B]">
                        Remembered your password?
                        <a href="{{ route('login') }}" class="text-[#C4B5FD] hover:text-[#8B5CF6] transition-colors font-medium">Sign In</a>
                    </p>
                </form>
            </div>

        </div>
    </section>
</main>
@endsection
