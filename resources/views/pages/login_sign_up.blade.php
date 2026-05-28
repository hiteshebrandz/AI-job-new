@extends('layouts.app')

@section('title', 'Secure Access')

@section('body-class', 'bg-background text-on-background min-h-screen flex font-body-md')

@section('page-css', 'login_sign_up.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
<!-- Split Screen Container -->
<main class="flex w-full min-h-screen">
<!-- Left Side: Visual & Quote (Suppresses Nav) -->
<section class="hidden lg:flex lg:w-1/2 gradient-mesh relative overflow-hidden items-center justify-center p-container-margin">
<!-- Decorative Elements -->
<div class="absolute inset-0 opacity-20">
<div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] rounded-full bg-secondary-fixed blur-[120px]"></div>
<div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-on-secondary-fixed-variant blur-[120px]"></div>
</div>
<div class="relative z-10 max-w-xl text-center lg:text-left">
<!-- Branding Anchor -->
<a href="{{ route('landing') }}" class="flex items-center gap-3 mb-12 hover:opacity-90 transition-opacity">
<div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">dataset</span>
</div>
<span class="font-headline-lg text-headline-lg font-bold text-white tracking-tight">{{ config('app.name') }}</span>
</a>
<h1 class="font-display-lg text-display-lg text-white mb-8">
                    Redefining the <span class="text-secondary-fixed">Future of Hiring</span>
</h1>
<div class="glass-panel p-card-padding rounded-xl">
<p class="font-title-md text-title-md text-white/90 italic mb-4 leading-relaxed">
                        "The most sophisticated way to manage your human capital is not just about data, but about the seamless synthesis of intelligence and empathy."
                    </p>
<div class="flex items-center gap-4">
<img alt="Profile" class="w-12 h-12 rounded-full border-2 border-white/20" data-alt="A professional headshot of a female executive in her late 40s, looking confidently at the camera. She is wearing a structured navy blazer in a high-end corporate office setting with soft, warm bokeh lighting. The overall mood is authoritative, trustworthy, and elegantly modern, fitting a premium SaaS platform aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCMiUhqu6HY7rzM04wzfa9HR9okfW5qL3_olIaGBxA3kW_0neE8U_lA-0vyHsBHpKbIUBIjXCwEKtflCwsDxX_8BwOykpAm93maDkkWPf-Vs9LFwCtCHRXDomODiugAlYh4zhLzoa6w36TsxT1cKLePiMWWPCZFUixeHj4PPqttbGyXmgxBzcXXe7n8bslmFyZtYS3LGf-t0jP0L5ecifKbdAe-aI5t6qtCooPzuKa9j43V7YliLkeq5afrWYzPp14eBH54VFyRhlJf"/>
<div>
<p class="font-label-caps text-label-caps text-white">Chief Talent Officer</p>
<p class="font-body-sm text-body-sm text-white/70">Executive Suite, {{ config('app.name') }}</p>
</div>
</div>
</div>
</div>
<!-- Abstract Graphic Placeholder -->
<div class="absolute bottom-10 right-10 opacity-30">
<span class="material-symbols-outlined text-[240px] text-white">all_inclusive</span>
</div>
</section>
<!-- Right Side: Login/Signup Canvas -->
<section class="w-full lg:w-1/2 flex items-center justify-center p-gutter bg-surface">
<div class="w-full max-w-[480px]">
<!-- Toggle Navigation -->
<div class="flex bg-surface-container-low p-1 rounded-xl mb-10 w-fit mx-auto lg:mx-0">
<button class="px-8 py-2.5 rounded-lg font-label-caps text-label-caps transition-all bg-white shadow-sm text-primary">
                        LOGIN
                    </button>
<a href="{{ route('hr.dashboard') }}" class="px-8 py-2.5 rounded-lg font-label-caps text-label-caps transition-all text-on-surface-variant hover:text-primary inline-block">
                        EMPLOYER
                    </a>
</div>
<!-- Form Card -->
<div class="bg-white rounded-xl p-card-padding shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] border border-outline-variant">
<header class="mb-8">
<h2 class="font-headline-lg text-headline-lg text-primary mb-2">Welcome Back</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Enter your credentials to access the Executive Suite.</p>
</header>
<!-- Social Logins -->
<div class="grid grid-cols-2 gap-4 mb-8">
<button class="flex items-center justify-center gap-2 py-3 px-4 border border-outline-variant rounded-xl hover:bg-surface-container-low transition-colors group">
<img alt="Google" class="w-5 h-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBUMFIfBGktpis-QvUFCYkjaPMZEdbm8HOkeSjWaIdGxjhmodNL8yfMMrQcPp1Mvvkc74FWPYy_PROWcSoP21z-4CuJd4jeiz_1Y1X5uyeEgE8kvZTpPruCRS0NQ81gYZxbbZy97-jdU6jCZKBeRGp2GK-JKgvy8hXphUY5vvQtjQkTDlKkSUObtxGzTyBosID2_Rg4_tsRUDN4FX8jbFKpSEbnpbu7aqi3PHI0aRRbVSF_gyjJzUrHr8iIFPB9HHIsJSXSjWAkrBec"/>
<span class="font-label-caps text-label-caps text-on-surface-variant group-hover:text-primary">Google</span>
</button>
<button class="flex items-center justify-center gap-2 py-3 px-4 border border-outline-variant rounded-xl hover:bg-surface-container-low transition-colors group">
<svg class="w-5 h-5 text-[#0A66C2]" fill="currentColor" viewbox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"></path></svg>
<span class="font-label-caps text-label-caps text-on-surface-variant group-hover:text-primary">LinkedIn</span>
</button>
</div>
<div class="relative flex items-center mb-8">
<div class="flex-grow border-t border-outline-variant"></div>
<span class="flex-shrink mx-4 font-label-caps text-label-caps text-outline">OR WITH EMAIL</span>
<div class="flex-grow border-t border-outline-variant"></div>
</div>
<!-- Email/Password Form -->
<form class="space-y-6" onsubmit="return false;">
<div>
<label class="block font-label-caps text-label-caps text-on-surface mb-2">CORPORATE EMAIL</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">mail</span>
<input class="w-full pl-12 pr-4 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all font-body-md text-on-surface" placeholder="name@@company.com" type="email"/>
</div>
</div>
<div>
<div class="flex justify-between items-center mb-2">
<label class="block font-label-caps text-label-caps text-on-surface">PASSWORD</label>
<a class="font-label-caps text-label-caps text-secondary hover:underline" href="{{ route('login') }}">FORGOT?</a>
</div>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
<input class="w-full pl-12 pr-12 py-3 bg-surface-container-low border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all font-body-md text-on-surface" placeholder="••••••••" type="password"/>
<button class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-primary" type="button">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
</div>
<div class="flex items-center gap-3">
<input class="w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary/20" id="remember" type="checkbox"/>
<label class="font-body-sm text-body-sm text-on-surface-variant" for="remember">Keep me signed in for 30 days</label>
</div>
<a href="{{ route('user.dashboard') }}" class="w-full py-4 bg-gradient-to-r from-secondary to-[#6063ee] text-white rounded-xl font-title-md text-title-md shadow-lg shadow-secondary/20 hover:scale-[1.02] active:scale-[0.98] transition-all block text-center">
                            Sign In to {{ config('app.name') }}
                        </a>
</form>
</div>
<footer class="mt-12 text-center lg:text-left">
<p class="font-body-sm text-body-sm text-on-surface-variant">
                        By continuing, you agree to the {{ config('app.name') }} 
                        <a class="text-secondary hover:underline" href="{{ route('landing') }}">Terms of Service</a> and 
                        <a class="text-secondary hover:underline" href="{{ route('landing') }}">Privacy Policy</a>.
                    </p>
</footer>
</div>
</section>
</main>
<!-- Global Footer (Suppressed on Login for focus, but referenced here for structure) -->
<!-- The split layout replaces the standard footer for this transactional screen -->
@endsection
