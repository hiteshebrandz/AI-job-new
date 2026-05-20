@extends('layouts.app')

@section('title', 'Sign In')

@section('body-class', 'bg-background text-on-background min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<main class="flex w-full min-h-screen">
    @include('auth._panel')

    <!-- Form side -->
    <section class="w-full lg:w-[45%] flex items-center justify-center p-8 bg-background">
        <div class="w-full max-w-[420px] animate-fade-in">
            <!-- Tab switcher -->
            <div class="flex bg-[#1E293B] p-1 rounded-2xl mb-8 w-fit">
                <span class="px-7 py-2.5 rounded-xl text-[13px] font-semibold bg-[#263248] text-[#E2E8F0] shadow-sm">Sign In</span>
                <a href="{{ route('register') }}" class="px-7 py-2.5 rounded-xl text-[13px] font-semibold text-[#64748B] hover:text-[#C4B5FD] transition-colors">Register</a>
            </div>

            <!-- Card -->
            <div class="glass-card p-8">
                <div class="mb-7">
                    <h2 class="text-[28px] font-bold text-[#E2E8F0] mb-1">Welcome back</h2>
                    <p class="text-[14px] text-[#64748B]">Sign in as User, HR, or Super Admin.</p>
                </div>

                @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-[#450A0A]/60 border border-[#F87171]/30">
                    <ul class="text-[13px] text-[#FCA5A5] space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-widest mb-2">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#475569] text-[18px]">mail</span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                class="input-dark pl-11"
                                placeholder="you@company.com"/>
                        </div>
                        @error('email')
                        <p class="mt-1.5 text-[12px] text-[#F87171]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#475569] text-[18px]">lock</span>
                            <input id="password" name="password" type="password" required
                                class="input-dark pl-11 pr-12"
                                placeholder="••••••••"/>
                            <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#475569] hover:text-[#94A3B8] transition-colors" tabindex="-1">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember + forgot -->
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <input class="w-4 h-4 rounded border-[#334155] bg-[#1E293B] text-[#8B5CF6] focus:ring-[#8B5CF6]/20" id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}/>
                            <label class="text-[13px] text-[#64748B]" for="remember">Keep me signed in</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-[13px] text-[#8B5CF6] hover:text-[#C4B5FD] transition-colors">Forgot password?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary w-full py-3.5 text-[15px] font-semibold">
                        <span class="material-symbols-outlined text-[18px]">login</span>
                        Sign In to Elements
                    </button>
                </form>

                <p class="mt-6 text-center text-[13px] text-[#64748B]">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-[#8B5CF6] font-semibold hover:text-[#C4B5FD] transition-colors">Create one free</a>
                </p>
            </div>

            <!-- Trust badges -->
            <div class="mt-6 flex items-center justify-center gap-6">
                <div class="flex items-center gap-1.5 text-[#334155]">
                    <span class="material-symbols-outlined text-[16px]">shield</span>
                    <span class="text-[12px]">Secure</span>
                </div>
                <div class="w-px h-4 bg-[#1E293B]"></div>
                <div class="flex items-center gap-1.5 text-[#334155]">
                    <span class="material-symbols-outlined text-[16px]">lock</span>
                    <span class="text-[12px]">Encrypted</span>
                </div>
                <div class="w-px h-4 bg-[#1E293B]"></div>
                <div class="flex items-center gap-1.5 text-[#334155]">
                    <span class="material-symbols-outlined text-[16px]">verified_user</span>
                    <span class="text-[12px]">GDPR Ready</span>
                </div>
            </div>
        </div>
    </section>
</main>

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    var input = document.getElementById(fieldId);
    var icon = btn.querySelector('.material-symbols-outlined');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endpush
@endsection
