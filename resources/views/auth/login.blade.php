@extends('layouts.app')

@section('title', 'Login')

@section('body-class', 'bg-background text-on-background min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<main class="flex w-full min-h-screen">
    @include('auth._panel')

    <section class="w-full lg:w-1/2 flex items-center justify-center p-gutter bg-surface">
        <div class="w-full max-w-[480px]">
            <div class="flex bg-surface-container-low p-1 rounded-xl mb-10 w-fit mx-auto lg:mx-0">
                <span class="px-8 py-2.5 rounded-lg font-label-caps text-label-caps bg-white shadow-sm text-primary">LOGIN</span>
                <a href="{{ route('register') }}" class="px-8 py-2.5 rounded-lg font-label-caps text-label-caps text-on-surface-variant hover:text-primary">REGISTER</a>
            </div>

            <div class="bg-white rounded-xl p-card-padding shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] border border-outline-variant">
                <header class="mb-8">
                    <h2 class="font-headline-lg text-headline-lg text-primary mb-2">Welcome Back</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Sign in as User, HR, or Super Admin.</p>
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

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email" class="block font-label-caps text-label-caps text-on-surface mb-2">EMAIL</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">mail</span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-12 pr-4 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all font-body-md text-on-surface @error('email') border-error @enderror"
                                placeholder="you@company.com"/>
                        </div>
                        @error('email')
                            <p class="mt-1 font-body-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block font-label-caps text-label-caps text-on-surface mb-2">PASSWORD</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                            <input id="password" name="password" type="password" required
                                class="w-full pl-12 pr-4 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all font-body-md text-on-surface"
                                placeholder="••••••••"/>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <input class="w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary/20" id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}/>
                        <label class="font-body-sm text-body-sm text-on-surface-variant" for="remember">Keep me signed in</label>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-secondary to-[#6063ee] text-white rounded-xl font-title-md text-title-md shadow-lg shadow-secondary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Sign In to Elements
                    </button>
                </form>

                <p class="mt-6 text-center font-body-sm text-on-surface-variant">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-secondary font-bold hover:underline">Register</a>
                </p>
            </div>
        </div>
    </section>
</main>
@endsection
