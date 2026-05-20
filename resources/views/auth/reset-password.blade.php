@extends('layouts.app')

@section('title', 'Reset Password')

@section('body-class', 'bg-[#0F172A] text-[#E2E8F0] min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<main class="flex w-full min-h-screen">
    @include('auth._panel')

    <section class="w-full lg:w-[45%] flex items-center justify-center p-8 bg-[#0F172A]">
        <div class="w-full max-w-[420px] animate-fade-in">

            <div class="mb-8">
                <h2 class="text-[28px] font-bold text-[#E2E8F0] mb-2">Set New Password</h2>
                <p class="text-[14px] text-[#64748B]">Choose a strong password for your account.</p>
            </div>

            <div class="glass-card p-8">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="block text-[12px] font-semibold uppercase tracking-wider text-[#64748B] mb-2">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $email) }}"
                            required
                            autofocus
                            class="input-dark"
                            placeholder="you@company.com"
                        >
                        @error('email')
                            <p class="mt-1.5 text-[12px] text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-[12px] font-semibold uppercase tracking-wider text-[#64748B] mb-2">New Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                class="input-dark pr-11"
                                placeholder="Min. 8 characters"
                            >
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#64748B] hover:text-[#94A3B8] transition-colors">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-[12px] text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[12px] font-semibold uppercase tracking-wider text-[#64748B] mb-2">Confirm Password</label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                class="input-dark pr-11"
                                placeholder="Repeat password"
                            >
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#64748B] hover:text-[#94A3B8] transition-colors">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3">
                        Reset Password
                    </button>
                </form>
            </div>

        </div>
    </section>
</main>

@push('scripts')
<script>
function togglePasswordVisibility(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon = btn.querySelector('.material-symbols-outlined');
    if (field.type === 'password') {
        field.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        field.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endpush
@endsection
