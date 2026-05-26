@extends('layouts.app')

@section('title', 'Create Account')

@section('body-class', 'bg-background text-on-background min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<main class="flex w-full min-h-screen">
    @include('auth._panel')

    <!-- Form side -->
    <section class="w-full lg:w-[45%] flex items-center justify-center p-8 overflow-y-auto" style="background:var(--bg-page);">
        <div class="w-full max-w-[420px] py-8 animate-fade-in">
            <!-- Tab switcher -->
            <div class="flex p-1 rounded-2xl mb-8 w-fit" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                <a href="{{ route('login') }}" class="px-7 py-2.5 rounded-xl text-[13px] font-semibold transition-colors" style="color:var(--text-muted);">Sign In</a>
                <span class="px-7 py-2.5 rounded-xl text-[13px] font-semibold shadow-sm" style="background:var(--bg-surface); color:var(--text-primary); border:1px solid var(--border-brand);">Register</span>
            </div>

            <!-- Card -->
            <div class="glass-card p-8">
                <div class="mb-7">
                    <h2 class="text-[28px] font-bold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Create Account</h2>
                    <p class="text-[14px]" style="color:var(--text-muted);">Register as a Candidate or HR professional.</p>
                </div>

                @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl" style="background:var(--badge-error-bg); border:1px solid rgba(186,26,26,0.25);">
                    <ul class="text-[13px] space-y-1" style="color:var(--badge-error-text);">
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
                        <label for="name" class="block text-[11px] font-semibold uppercase tracking-widest mb-2" style="color:var(--text-muted);">Full Name</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--text-muted);">person</span>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required class="input-dark pl-11" placeholder="Your full name"/>
                        </div>
                        @error('name')
                        <p class="mt-1.5 text-[12px]" style="color:var(--badge-error-text);">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-[11px] font-semibold uppercase tracking-widest mb-2" style="color:var(--text-muted);">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--text-muted);">mail</span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input-dark pl-11" placeholder="you@company.com"/>
                        </div>
                        @error('email')
                        <p class="mt-1.5 text-[12px]" style="color:var(--badge-error-text);">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-[11px] font-semibold uppercase tracking-widest mb-2" style="color:var(--text-muted);">Account Type</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--text-muted);">badge</span>
                            <select id="role" name="role" required class="input-dark pl-11 appearance-none cursor-pointer">
                                <option value="">Select account type…</option>
                                <option value="user" {{ old('role', request('role') === 'user' ? 'user' : '') === 'user' ? 'selected' : '' }}>Candidate (Job Seeker)</option>
                                <option value="hr"   {{ old('role', request('role') === 'hr'   ? 'hr'   : '') === 'hr'   ? 'selected' : '' }}>Employer / HR</option>
                            </select>
                        </div>
                        @error('role')
                        <p class="mt-1.5 text-[12px]" style="color:var(--badge-error-text);">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-[11px] font-semibold uppercase tracking-widest mb-2" style="color:var(--text-muted);">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--text-muted);">lock</span>
                            <input id="password" name="password" type="password" required class="input-dark pl-11 pr-12" placeholder="Min. 8 characters"/>
                            <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 transition-colors" style="color:var(--text-muted);" tabindex="-1">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-1.5 text-[12px]" style="color:var(--badge-error-text);">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-semibold uppercase tracking-widest mb-2" style="color:var(--text-muted);">Confirm Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--text-muted);">lock_reset</span>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="input-dark pl-11 pr-12" placeholder="Repeat your password"/>
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-4 top-1/2 -translate-y-1/2 transition-colors" style="color:var(--text-muted);" tabindex="-1">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3.5 text-[15px] font-semibold">
                        <span class="material-symbols-outlined text-[18px]">person_add</span>
                        Create Account
                    </button>
                </form>

                <p class="mt-6 text-center text-[13px]" style="color:var(--text-muted);">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold transition-colors hover:opacity-80" style="color:var(--brand-primary);">Sign in</a>
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
