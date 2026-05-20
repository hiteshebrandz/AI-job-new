@extends('layouts.app')

@section('title', 'Create Account')

@section('body-class', 'bg-background text-on-background min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<main class="flex w-full min-h-screen">
    @include('auth._panel')

    <!-- Form side -->
    <section class="w-full lg:w-[45%] flex items-center justify-center p-8 bg-background overflow-y-auto">
        <div class="w-full max-w-[420px] py-8 animate-fade-in">
            <!-- Tab switcher -->
            <div class="flex bg-[#1E293B] p-1 rounded-2xl mb-8 w-fit">
                <a href="{{ route('login') }}" class="px-7 py-2.5 rounded-xl text-[13px] font-semibold text-[#64748B] hover:text-[#C4B5FD] transition-colors">Sign In</a>
                <span class="px-7 py-2.5 rounded-xl text-[13px] font-semibold bg-[#263248] text-[#E2E8F0] shadow-sm">Register</span>
            </div>

            <!-- Card -->
            <div class="glass-card p-8">
                <div class="mb-7">
                    <h2 class="text-[28px] font-bold text-[#E2E8F0] mb-1">Create Account</h2>
                    <p class="text-[14px] text-[#64748B]">Register as a User or HR professional.</p>
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

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-widest mb-2">Full Name</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#475569] text-[18px]">person</span>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                class="input-dark pl-11" placeholder="Your full name"/>
                        </div>
                        @error('name')
                        <p class="mt-1.5 text-[12px] text-[#F87171]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-widest mb-2">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#475569] text-[18px]">mail</span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                class="input-dark pl-11" placeholder="you@company.com"/>
                        </div>
                        @error('email')
                        <p class="mt-1.5 text-[12px] text-[#F87171]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-widest mb-2">Account Type</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#475569] text-[18px]">badge</span>
                            <select id="role" name="role" required class="input-dark pl-11 appearance-none cursor-pointer">
                                <option value="" class="bg-[#1E293B]">Select account type…</option>
                                <option value="user" class="bg-[#1E293B]" {{ old('role') === 'user' ? 'selected' : '' }}>Candidate (Job Seeker)</option>
                                <option value="hr" class="bg-[#1E293B]" {{ old('role') === 'hr' ? 'selected' : '' }}>Employer / HR</option>
                            </select>
                        </div>
                        @error('role')
                        <p class="mt-1.5 text-[12px] text-[#F87171]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#475569] text-[18px]">lock</span>
                            <input id="password" name="password" type="password" required
                                class="input-dark pl-11 pr-12" placeholder="Min. 8 characters"/>
                            <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#475569] hover:text-[#94A3B8] transition-colors" tabindex="-1">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-1.5 text-[12px] text-[#F87171]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-widest mb-2">Confirm Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#475569] text-[18px]">lock_reset</span>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="input-dark pl-11 pr-12" placeholder="Repeat your password"/>
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#475569] hover:text-[#94A3B8] transition-colors" tabindex="-1">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3.5 text-[15px] font-semibold">
                        <span class="material-symbols-outlined text-[18px]">person_add</span>
                        Create Account
                    </button>
                </form>

                <p class="mt-6 text-center text-[13px] text-[#64748B]">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-[#8B5CF6] font-semibold hover:text-[#C4B5FD] transition-colors">Sign in</a>
                </p>
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
