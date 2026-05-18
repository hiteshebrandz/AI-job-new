@extends('layouts.app')

@section('title', 'Register')

@section('body-class', 'bg-background text-on-background min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<main class="flex w-full min-h-screen">
    @include('auth._panel')

    <section class="w-full lg:w-1/2 flex items-center justify-center p-gutter bg-surface">
        <div class="w-full max-w-[480px]">
            <div class="flex bg-surface-container-low p-1 rounded-xl mb-10 w-fit mx-auto lg:mx-0">
                <a href="{{ route('login') }}" class="px-8 py-2.5 rounded-lg font-label-caps text-label-caps text-on-surface-variant hover:text-primary">LOGIN</a>
                <span class="px-8 py-2.5 rounded-lg font-label-caps text-label-caps bg-white shadow-sm text-primary">REGISTER</span>
            </div>

            <div class="bg-white rounded-xl p-card-padding shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] border border-outline-variant">
                <header class="mb-8">
                    <h2 class="font-headline-lg text-headline-lg text-primary mb-2">Create Account</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Register as a User or HR. Super Admin accounts are created by administrators only.</p>
                </header>

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-error-container/20 border border-error/30">
                        <ul class="font-body-sm text-error space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="block font-label-caps text-label-caps text-on-surface mb-2">FULL NAME</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 font-body-md text-on-surface"/>
                        @error('name')
                            <p class="mt-1 font-body-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block font-label-caps text-label-caps text-on-surface mb-2">EMAIL</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 font-body-md text-on-surface"/>
                        @error('email')
                            <p class="mt-1 font-body-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="block font-label-caps text-label-caps text-on-surface mb-2">ACCOUNT TYPE</label>
                        <select id="role" name="role" required
                            class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 font-body-md text-on-surface">
                            <option value="">Select role...</option>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User (Job Seeker)</option>
                            <option value="hr" {{ old('role') === 'hr' ? 'selected' : '' }}>HR (Employer)</option>
                        </select>
                        @error('role')
                            <p class="mt-1 font-body-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block font-label-caps text-label-caps text-on-surface mb-2">PASSWORD</label>
                        <input id="password" name="password" type="password" required
                            class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 font-body-md text-on-surface"/>
                        @error('password')
                            <p class="mt-1 font-body-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-label-caps text-label-caps text-on-surface mb-2">CONFIRM PASSWORD</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 font-body-md text-on-surface"/>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-secondary to-[#6063ee] text-white rounded-xl font-title-md text-title-md shadow-lg shadow-secondary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Create Account
                    </button>
                </form>

                <p class="mt-6 text-center font-body-sm text-on-surface-variant">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-secondary font-bold hover:underline">Sign in</a>
                </p>
            </div>
        </div>
    </section>
</main>
@endsection
