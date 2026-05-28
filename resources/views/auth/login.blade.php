@extends('layouts.app')

@section('title', 'Sign In')

@section('body-class', 'bg-background text-on-background min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<main class="flex w-full min-h-screen">
    @include('auth._panel')

    <!-- Form side -->
    <section class="w-full lg:w-[45%] flex items-center justify-center p-8" style="background:var(--bg-page);">
        <div class="w-full max-w-[420px] animate-fade-in">
            <!-- Tab switcher -->
            <div class="flex p-1 rounded-2xl mb-8 w-fit" style="background:var(--bg-surface-low); border:1px solid var(--border-subtle);">
                <span class="px-7 py-2.5 rounded-xl text-[13px] font-semibold shadow-sm" style="background:var(--bg-surface); color:var(--text-primary); border:1px solid var(--border-brand);">Sign In</span>
                <a href="{{ route('register') }}" class="px-7 py-2.5 rounded-xl text-[13px] font-semibold transition-colors" style="color:var(--text-muted);">Register</a>
            </div>

            <!-- Card -->
            <div class="glass-card p-8">
                <div class="mb-7">
                    <h2 class="text-[28px] font-bold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:var(--text-heading);">Welcome back</h2>
                    <p class="text-[14px]" style="color:var(--text-muted);">Sign in as User, HR, or Super Admin.</p>
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

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-[11px] font-semibold uppercase tracking-widest mb-2" style="color:var(--text-muted);">Email Address</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--text-muted);">mail</span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                class="input-dark pl-11"
                                placeholder="you@company.com"/>
                        </div>
                        @error('email')
                        <p class="mt-1.5 text-[12px]" style="color:var(--badge-error-text);">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-[11px] font-semibold uppercase tracking-widest mb-2" style="color:var(--text-muted);">Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[18px]" style="color:var(--text-muted);">lock</span>
                            <input id="password" name="password" type="password" required
                                class="input-dark pl-11 pr-12"
                                placeholder="••••••••"/>
                            <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 transition-colors" style="color:var(--text-muted);" tabindex="-1">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember + forgot -->
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <input class="w-4 h-4 rounded" style="border-color:var(--border-default); accent-color:var(--brand-primary);" id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}/>
                            <label class="text-[13px]" style="color:var(--text-muted);" for="remember">Keep me signed in</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-[13px] font-medium transition-colors hover:opacity-80" style="color:var(--brand-primary);">Forgot password?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary w-full py-3.5 text-[15px] font-semibold">
                        <span class="material-symbols-outlined text-[18px]">login</span>
                        Sign In to {{ config('app.name') }}
                    </button>
                </form>

                <p class="mt-6 text-center text-[13px]" style="color:var(--text-muted);">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold transition-colors hover:opacity-80" style="color:var(--brand-primary);">Create one free</a>
                </p>
            </div>

            <!-- Trust badges -->
            <div class="mt-6 flex items-center justify-center gap-6">
                @foreach (['shield' => 'Secure', 'lock' => 'Encrypted', 'verified_user' => 'GDPR Ready'] as $icon => $label)
                <div class="flex items-center gap-1.5" style="color:var(--text-muted);">
                    <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
                    <span class="text-[12px]">{{ $label }}</span>
                </div>
                @if (!$loop->last)
                <div class="w-px h-4" style="background:var(--border-subtle);"></div>
                @endif
                @endforeach
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
