@extends('layouts.app')

@section('title', 'Candidate Dashboard')

@section('body-class', 'bg-background text-on-surface font-body-md')

@section('page-css', 'candidate_dashboard.css')

@section('tailwind-config', 'tailwind-config-candidate.js')

@section('content')
<!-- SideNavBar (Shared Component) -->
@include('partials.nav.candidate-sidebar')
<!-- Main Content Area -->
<main class="ml-[280px] min-h-screen p-container-margin max-w-[1440px]">
<!-- TopNavBar (Shared Component Equivalent) -->
<header class="flex justify-between items-center mb-8">
<div class="flex-1 max-w-xl">
<div class="relative group">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline" data-icon="search">search</span>
<input class="w-full pl-12 pr-4 py-3 bg-white border border-outline-variant rounded-xl focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all" placeholder="Search for opportunities, mentors, or companies..." type="text"/>
</div>
</div>
<div class="flex items-center gap-6 ml-gutter">
<button class="relative p-2 text-on-surface-variant hover:text-secondary transition-colors">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-secondary rounded-full"></span>
</button>
<a href="{{ route('user.jobs.recommendations') }}" class="bg-secondary text-white px-6 py-2.5 rounded-xl font-title-md text-body-sm font-bold shadow-lg hover:shadow-secondary/20 transition-all active:scale-[0.98] inline-block">
                    Explore Jobs
                </a>
</div>
</header>
<!-- Welcome Banner -->
<section class="relative overflow-hidden rounded-[2rem] bg-primary-container p-12 mb-8 text-white min-h-[240px] flex flex-col justify-center">
<div class="relative z-10 max-w-2xl">
<h2 class="font-headline-lg text-headline-lg mb-2">Welcome back, Alex Johnson!</h2>
<p class="text-on-primary-container font-body-md opacity-90 mb-6">Your profile is currently outperforming 85% of candidates in your field. 12 new roles matching your expertise were posted today.</p>
<div class="flex gap-4">
<a href="{{ route('user.resume.upload') }}" class="bg-white text-primary px-6 py-2 rounded-lg font-bold text-body-sm inline-block">Complete Profile</a>
<a href="{{ route('user.resume.analytics') }}" class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-6 py-2 rounded-lg font-bold text-body-sm inline-block">View Status</a>
</div>
</div>
<!-- Abstract Background Element -->
<div class="absolute right-0 top-0 w-1/3 h-full bg-gradient-to-l from-secondary/40 to-transparent flex items-center justify-center">
<div class="w-48 h-48 rounded-full border-[12px] border-white/5 animate-pulse"></div>
</div>
</section>
<!-- Bento Grid Layout -->
<div class="grid grid-cols-12 gap-gutter">
<!-- KPI Cards -->
<div class="col-span-12 lg:col-span-8 grid grid-cols-2 md:grid-cols-4 gap-4">
<div class="glass-card p-6 rounded-2xl">
<p class="font-label-caps text-label-caps text-on-surface-variant mb-1 uppercase">Applications</p>
<div class="flex items-end justify-between">
<span class="font-headline-lg text-headline-lg text-primary">24</span>
<span class="text-secondary font-bold text-xs flex items-center mb-2">
<span class="material-symbols-outlined text-sm" data-icon="trending_up">trending_up</span> +12%
                        </span>
</div>
</div>
<div class="glass-card p-6 rounded-2xl">
<p class="font-label-caps text-label-caps text-on-surface-variant mb-1 uppercase">Profile Views</p>
<div class="flex items-end justify-between">
<span class="font-headline-lg text-headline-lg text-primary">142</span>
<span class="text-secondary font-bold text-xs flex items-center mb-2">
<span class="material-symbols-outlined text-sm" data-icon="trending_up">trending_up</span> +8%
                        </span>
</div>
</div>
<div class="glass-card p-6 rounded-2xl">
<p class="font-label-caps text-label-caps text-on-surface-variant mb-1 uppercase">Saved Jobs</p>
<div class="flex items-end justify-between">
<span class="font-headline-lg text-headline-lg text-primary">09</span>
<span class="text-outline font-bold text-xs flex items-center mb-2">--</span>
</div>
</div>
<div class="glass-card p-6 rounded-2xl">
<p class="font-label-caps text-label-caps text-on-surface-variant mb-1 uppercase">Interviews</p>
<div class="flex items-end justify-between">
<span class="font-headline-lg text-headline-lg text-primary">03</span>
<span class="text-error font-bold text-xs flex items-center mb-2">
<span class="material-symbols-outlined text-sm" data-icon="calendar_today">calendar_today</span> Upcoming
                        </span>
</div>
</div>
</div>
<!-- Match Score Card -->
<div class="col-span-12 lg:col-span-4 row-span-2 glass-card rounded-2xl p-card-padding flex flex-col items-center text-center justify-center">
<h3 class="font-title-md text-title-md mb-6 w-full text-left">Resume Match Strength</h3>
<div class="relative w-48 h-48 mb-6">
<svg class="w-full h-full transform -rotate-90">
<circle class="text-surface-variant" cx="96" cy="96" fill="transparent" r="88" stroke="currentColor" stroke-width="12"></circle>
<circle class="text-secondary" cx="96" cy="96" fill="transparent" r="88" stroke="currentColor" stroke-dasharray="553" stroke-dashoffset="83" stroke-linecap="round" stroke-width="12"></circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center">
<span class="font-headline-lg text-headline-lg text-primary">85%</span>
<span class="font-label-caps text-label-caps text-on-surface-variant">OPTIMIZED</span>
</div>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-6">Your resume is highly compatible with <span class="text-secondary font-bold">Product Management</span> roles at Fortune 500 companies.</p>
<a href="{{ route('user.resume.analytics') }}" class="w-full py-3 rounded-xl border border-secondary text-secondary font-bold hover:bg-secondary/5 transition-colors block text-center">Analyze Full Resume</a>
</div>
<!-- Recommended Cards -->
<div class="col-span-12 lg:col-span-8 space-y-4">
<div class="flex items-center justify-between">
<h3 class="font-title-md text-title-md">Recommended for You</h3>
<a class="text-secondary font-bold text-body-sm hover:underline" href="{{ route('user.jobs.recommendations') }}">View All</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<!-- Mini Card 1 -->
<a href="{{ route('user.jobs.show', 1) }}" class="glass-card p-4 rounded-xl flex gap-4 hover:shadow-md transition-shadow cursor-pointer border-l-4 border-secondary block">
<div class="w-12 h-12 bg-surface-container rounded-lg flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary" data-icon="storefront">storefront</span>
</div>
<div class="flex-1">
<h4 class="font-bold text-body-md">Senior UX Lead</h4>
<p class="font-body-sm text-on-surface-variant">Global Tech Corp • San Francisco</p>
<div class="flex gap-2 mt-2">
<span class="bg-secondary-fixed text-on-secondary-fixed-variant px-2 py-0.5 rounded text-[10px] font-bold uppercase">Remote</span>
<span class="bg-surface-variant text-on-surface-variant px-2 py-0.5 rounded text-[10px] font-bold uppercase">$180k - $220k</span>
</div>
</div>
</a>
<!-- Mini Card 2 -->
<a href="{{ route('user.jobs.show', 2) }}" class="glass-card p-4 rounded-xl flex gap-4 hover:shadow-md transition-shadow cursor-pointer block">
<div class="w-12 h-12 bg-surface-container rounded-lg flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary" data-icon="rocket_launch">rocket_launch</span>
</div>
<div class="flex-1">
<h4 class="font-bold text-body-md">Product Strategist</h4>
<p class="font-body-sm text-on-surface-variant">FinScale AI • London</p>
<div class="flex gap-2 mt-2">
<span class="bg-secondary-fixed text-on-secondary-fixed-variant px-2 py-0.5 rounded text-[10px] font-bold uppercase">Hybrid</span>
<span class="bg-surface-variant text-on-surface-variant px-2 py-0.5 rounded text-[10px] font-bold uppercase">$140k - $160k</span>
</div>
</div>
</a>
</div>
</div>
<!-- Activity Feed -->
<div class="col-span-12 glass-card rounded-2xl overflow-hidden">
<div class="px-card-padding py-4 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
<h3 class="font-title-md text-title-md">Recent Activity</h3>
<button class="text-on-surface-variant hover:text-primary transition-colors">
<span class="material-symbols-outlined" data-icon="more_horiz">more_horiz</span>
</button>
</div>
<div class="divide-y divide-outline-variant">
<div class="px-card-padding py-4 flex items-center gap-4 hover:bg-surface-container-high/30 transition-colors">
<div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700">
<span class="material-symbols-outlined text-sm" data-icon="check_circle" style="font-variation-settings: 'FILL' 1;">check_circle</span>
</div>
<div class="flex-1">
<p class="font-body-md">Application Viewed: <span class="font-bold">Google Cloud Engineering</span></p>
<p class="font-body-sm text-on-surface-variant">Your application was reviewed by HR 2 hours ago.</p>
</div>
<span class="font-label-caps text-label-caps text-outline">2h ago</span>
</div>
<div class="px-card-padding py-4 flex items-center gap-4 hover:bg-surface-container-high/30 transition-colors">
<div class="w-8 h-8 rounded-full bg-secondary-fixed flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-sm" data-icon="mail">mail</span>
</div>
<div class="flex-1">
<p class="font-body-md">New Message from <span class="font-bold">Sarah Williams</span></p>
<p class="font-body-sm text-on-surface-variant">"We'd love to schedule a preliminary screening for..."</p>
</div>
<span class="font-label-caps text-label-caps text-outline">5h ago</span>
</div>
<div class="px-card-padding py-4 flex items-center gap-4 hover:bg-surface-container-high/30 transition-colors">
<div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-700">
<span class="material-symbols-outlined text-sm" data-icon="bolt">bolt</span>
</div>
<div class="flex-1">
<p class="font-body-md">Auto-Matching Update</p>
<p class="font-body-sm text-on-surface-variant">3 new jobs match your updated 'Remote' preference.</p>
</div>
<span class="font-label-caps text-label-caps text-outline">Yesterday</span>
</div>
</div>
</div>
</div>
<!-- Footer (Shared Component) -->
<footer class="mt-16 w-full py-8 border-t border-outline-variant flex flex-col md:flex-row justify-between items-center px-4">
<div class="mb-4 md:mb-0">
<p class="font-title-md text-title-md font-bold text-primary">Elements HR</p>
<p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">© 2024 Elements HR Services. All rights reserved.</p>
</div>
<div class="flex gap-8">
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Terms of Service</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('login') }}">Contact Support</a>
</div>
</footer>
</main>
<!-- Contextual FAB (Logic Applied: Suppressed on focused dashboards unless critical) -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-secondary text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-[60]">
<span class="material-symbols-outlined" data-icon="chat_bubble">chat_bubble</span>
</button>
@endsection
