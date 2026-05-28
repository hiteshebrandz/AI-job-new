@extends('layouts.app')

@section('title', 'Admin Analytics Dashboard')

@section('body-class', 'bg-background text-on-background font-body-md selection:bg-secondary/20')

@section('page-css', 'admin_analytics_dashboard.css')

@section('tailwind-config', 'tailwind-config-admin.js')

@section('content')
<!-- Sidebar Navigation -->
@include('partials.nav.employer-sidebar')
<!-- Main Content Canvas -->
<main class="lg:ml-[280px] min-h-screen">
<!-- Top Navigation Bar -->
<header class="fixed top-0 right-0 lg:w-[calc(100%-280px)] w-full h-[64px] z-40 glass-panel border-b border-outline-variant flex justify-between items-center px-6 lg:px-8 max-w-[1440px] mx-auto">
<div class="flex items-center gap-4 flex-1">
<div class="relative w-full max-w-md group">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60 group-focus-within:text-secondary transition-colors" data-icon="search">search</span>
<input class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-secondary/50 text-body-sm" placeholder="Search analytics, reports, or users..." type="text"/>
</div>
</div>
<div class="flex items-center gap-4">
<button class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors relative">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full"></span>
</button>
<div class="h-8 w-[1px] bg-outline-variant mx-2"></div>
<div class="flex gap-2">
<button class="flex items-center gap-2 px-4 py-2 border border-outline-variant rounded-xl text-label-caps font-label-caps hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-[18px]" data-icon="download">download</span>
                        Export PDF
                    </button>
<button class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl text-label-caps font-label-caps hover:opacity-90 transition-opacity active:scale-[0.98]">
<span class="material-symbols-outlined text-[18px]" data-icon="csv">description</span>
                        CSV Report
                    </button>
</div>
</div>
</header>
<!-- Dashboard Content -->
<div class="pt-24 pb-12 px-container-margin max-w-[1440px] mx-auto">
<!-- Welcome Header -->
<div class="mb-8">
<h2 class="font-headline-lg text-headline-lg mb-2">Executive Analytics Overview</h2>
<p class="font-body-md text-on-surface-variant">Real-time system-wide performance and recruitment velocity metrics.</p>
</div>
<!-- Bento Grid - Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-gutter mb-gutter">
<div class="glass-card p-card-padding rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-secondary/10 rounded-lg text-secondary">
<span class="material-symbols-outlined" data-icon="schedule">schedule</span>
</div>
<span class="text-error font-label-caps bg-error/10 px-2 py-1 rounded-full text-[10px]">-12% vs LY</span>
</div>
<p class="text-label-caps font-label-caps text-on-surface-variant mb-1">TIME TO HIRE</p>
<h3 class="text-headline-lg font-headline-lg">18.4 <span class="text-body-sm font-normal text-on-surface-variant">Days</span></h3>
</div>
<div class="glass-card p-card-padding rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-secondary/10 rounded-lg text-secondary">
<span class="material-symbols-outlined" data-icon="trending_up">trending_up</span>
</div>
<span class="text-secondary font-label-caps bg-secondary/10 px-2 py-1 rounded-full text-[10px]">+8.2%</span>
</div>
<p class="text-label-caps font-label-caps text-on-surface-variant mb-1">SOURCE QUALITY</p>
<h3 class="text-headline-lg font-headline-lg">84 <span class="text-body-sm font-normal text-on-surface-variant">Index</span></h3>
</div>
<div class="glass-card p-card-padding rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-secondary/10 rounded-lg text-secondary">
<span class="material-symbols-outlined" data-icon="diversity_3">diversity_3</span>
</div>
<span class="text-secondary font-label-caps bg-secondary/10 px-2 py-1 rounded-full text-[10px]">On Track</span>
</div>
<p class="text-label-caps font-label-caps text-on-surface-variant mb-1">DIVERSITY SCORE</p>
<h3 class="text-headline-lg font-headline-lg">92%</h3>
</div>
<div class="glass-card p-card-padding rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<div class="flex justify-between items-start mb-4">
<div class="p-2 bg-secondary/10 rounded-lg text-secondary">
<span class="material-symbols-outlined" data-icon="person_add">person_add</span>
</div>
<span class="text-secondary font-label-caps bg-secondary/10 px-2 py-1 rounded-full text-[10px]">Live</span>
</div>
<p class="text-label-caps font-label-caps text-on-surface-variant mb-1">TOTAL USERS</p>
<h3 class="text-headline-lg font-headline-lg">{{ number_format($totalUsers) }}</h3>
</div>
</div>
<!-- Visualization Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-gutter">
<!-- Main Trend Chart -->
<div class="md:col-span-2 glass-card p-card-padding rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] h-[400px] flex flex-col">
<div class="flex justify-between items-center mb-8">
<h4 class="font-title-md text-title-md">Time to Hire Trends</h4>
<div class="flex gap-2">
<button class="px-3 py-1 bg-surface-container-high rounded-full text-label-caps font-label-caps">6 Months</button>
<button class="px-3 py-1 hover:bg-surface-container-high rounded-full text-label-caps font-label-caps transition-colors">1 Year</button>
</div>
</div>
<div class="flex-1 relative flex items-end justify-between gap-4 pb-6 px-4">
<!-- Abstract Chart Representation -->
<div class="w-12 bg-surface-container h-[40%] rounded-t-lg relative group">
<div class="absolute inset-0 bg-gradient-primary opacity-0 group-hover:opacity-100 transition-opacity rounded-t-lg"></div>
</div>
<div class="w-12 bg-surface-container h-[65%] rounded-t-lg relative group">
<div class="absolute inset-0 bg-gradient-primary opacity-0 group-hover:opacity-100 transition-opacity rounded-t-lg"></div>
</div>
<div class="w-12 bg-surface-container h-[55%] rounded-t-lg relative group">
<div class="absolute inset-0 bg-gradient-primary opacity-0 group-hover:opacity-100 transition-opacity rounded-t-lg"></div>
</div>
<div class="w-12 bg-surface-container h-[80%] rounded-t-lg relative group">
<div class="absolute inset-0 bg-gradient-primary opacity-0 group-hover:opacity-100 transition-opacity rounded-t-lg"></div>
</div>
<div class="w-12 bg-surface-container h-[45%] rounded-t-lg relative group">
<div class="absolute inset-0 bg-gradient-primary opacity-0 group-hover:opacity-100 transition-opacity rounded-t-lg"></div>
</div>
<div class="w-12 bg-gradient-primary h-[90%] rounded-t-lg shadow-lg"></div>
<!-- Axis Labels -->
<div class="absolute bottom-0 left-0 right-0 flex justify-between px-4 text-label-caps font-label-caps opacity-50 pt-2 border-t border-outline-variant">
<span>JAN</span><span>FEB</span><span>MAR</span><span>APR</span><span>MAY</span><span>JUN</span>
</div>
</div>
</div>
<!-- Source Quality Breakdown -->
<div class="glass-card p-card-padding rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] h-[400px] flex flex-col">
<h4 class="font-title-md text-title-md mb-8">Source Quality</h4>
<div class="flex-1 flex flex-col justify-center space-y-6">
<div class="space-y-2">
<div class="flex justify-between text-body-sm font-semibold">
<span>LinkedIn Premium</span>
<span>42%</span>
</div>
<div class="h-2 w-full bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-secondary w-[42%]"></div>
</div>
</div>
<div class="space-y-2">
<div class="flex justify-between text-body-sm font-semibold">
<span>Internal Referrals</span>
<span>38%</span>
</div>
<div class="h-2 w-full bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-[#9333ea] w-[38%]"></div>
</div>
</div>
<div class="space-y-2">
<div class="flex justify-between text-body-sm font-semibold">
<span>Global Job Boards</span>
<span>15%</span>
</div>
<div class="h-2 w-full bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-[#c0c1ff] w-[15%]"></div>
</div>
</div>
<div class="space-y-2">
<div class="flex justify-between text-body-sm font-semibold">
<span>Direct Sourcing</span>
<span>5%</span>
</div>
<div class="h-2 w-full bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-surface-dim w-[5%]"></div>
</div>
</div>
</div>
</div>
</div>
<!-- Bottom Row: Diversity & Recent Transactions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
<!-- Diversity Metrics -->
<div class="glass-card p-card-padding rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<div class="flex justify-between items-center mb-6">
<h4 class="font-title-md text-title-md">Diversity Metrics</h4>
<span class="material-symbols-outlined text-secondary" data-icon="info">info</span>
</div>
<div class="flex items-center gap-12">
<!-- Circular Indicator -->
<div class="relative w-32 h-32 flex items-center justify-center">
<svg class="w-full h-full -rotate-90">
<circle class="text-surface-container-high" cx="64" cy="64" fill="transparent" r="58" stroke="currentColor" stroke-width="12"></circle>
<circle class="text-secondary" cx="64" cy="64" fill="transparent" r="58" stroke="currentColor" stroke-dasharray="364" stroke-dashoffset="29" stroke-width="12"></circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center">
<span class="font-headline-lg text-title-md">92%</span>
<span class="text-[10px] font-label-caps opacity-60">SCORE</span>
</div>
</div>
<div class="flex-1 space-y-4">
<div class="flex items-center gap-3">
<div class="w-3 h-3 rounded-full bg-secondary"></div>
<span class="font-body-sm flex-1">Gender Representation</span>
<span class="font-body-sm font-bold">88%</span>
</div>
<div class="flex items-center gap-3">
<div class="w-3 h-3 rounded-full bg-[#9333ea]"></div>
<span class="font-body-sm flex-1">Ethnicity &amp; Heritage</span>
<span class="font-body-sm font-bold">94%</span>
</div>
<div class="flex items-center gap-3">
<div class="w-3 h-3 rounded-full bg-[#c0c1ff]"></div>
<span class="font-body-sm flex-1">Veterans &amp; Disability</span>
<span class="font-body-sm font-bold">91%</span>
</div>
</div>
</div>
</div>
<!-- Strategic Insights -->
<div class="bg-primary-container text-on-primary-container p-card-padding rounded-xl shadow-xl overflow-hidden relative group">
<!-- Subtle background visual -->
<div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-secondary/10 rounded-full blur-3xl group-hover:bg-secondary/20 transition-colors"></div>
<div class="relative z-10">
<h4 class="font-title-md text-title-md text-white mb-2">Executive Insight</h4>
<p class="font-body-md text-on-primary-container/80 mb-6 leading-relaxed">
                            Based on Q3 recruitment velocity, the current 'Time to Hire' is 14% faster than the industry benchmark for Fortune 500 tech firms. Referral quality remains the highest-performing channel for retention.
                        </p>
<div class="flex items-center gap-4">
<div class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-lg border border-white/10">
<p class="text-label-caps font-label-caps text-white/60">PREDICTION</p>
<p class="font-body-md font-bold text-white">Scale +22% by Q4</p>
</div>
<button class="flex-1 py-3 bg-secondary text-white font-semibold text-[12px] rounded-lg hover:opacity-90 transition-colors">
                                View Strategic Roadmap
                            </button>
</div>
</div>
</div>
</div>
</div>
<!-- System-wide Data Table -->
<section class="px-container-margin max-w-[1440px] mx-auto pb-24">
<div class="glass-card rounded-xl overflow-hidden shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)]">
<div class="p-card-padding flex justify-between items-center border-b border-outline-variant">
<h4 class="font-title-md text-title-md">Recent Global Activity</h4>
<div class="flex gap-2">
<button class="p-2 hover:bg-surface-container rounded-lg transition-colors">
<span class="material-symbols-outlined" data-icon="filter_list">filter_list</span>
</button>
<button class="p-2 hover:bg-surface-container rounded-lg transition-colors">
<span class="material-symbols-outlined" data-icon="more_vert">more_vert</span>
</button>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low border-b border-outline-variant">
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">REGION</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">DEPARTMENT</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">HIRE VELOCITY</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">RETENTION</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">STATUS</th>
</tr>
</thead>
<tbody class="font-body-sm">
<tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors">
<td class="px-6 py-4">North America</td>
<td class="px-6 py-4 font-semibold">Engineering</td>
<td class="px-6 py-4">14.2 Days</td>
<td class="px-6 py-4">98.2%</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-bold uppercase tracking-wider">Optimal</span>
</td>
</tr>
<tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors">
<td class="px-6 py-4">EMEA Central</td>
<td class="px-6 py-4 font-semibold">Product Design</td>
<td class="px-6 py-4">22.1 Days</td>
<td class="px-6 py-4">94.5%</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-bold uppercase tracking-wider">Optimal</span>
</td>
</tr>
<tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors">
<td class="px-6 py-4">APAC Hub</td>
<td class="px-6 py-4 font-semibold">Sales Operations</td>
<td class="px-6 py-4">31.8 Days</td>
<td class="px-6 py-4">88.1%</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold uppercase tracking-wider">Warning</span>
</td>
</tr>
<tr class="hover:bg-surface-container-low transition-colors">
<td class="px-6 py-4">Latin America</td>
<td class="px-6 py-4 font-semibold">Customer Success</td>
<td class="px-6 py-4">19.5 Days</td>
<td class="px-6 py-4">96.7%</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-bold uppercase tracking-wider">Optimal</span>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</section>
<!-- Footer -->
<footer class="w-full py-8 border-t border-outline-variant bg-surface mt-12">
<div class="flex flex-col md:flex-row justify-between items-center px-container-margin max-w-7xl mx-auto">
<div class="mb-4 md:mb-0">
<p class="font-title-md text-title-md font-bold text-primary">{{ config('app.name') }}</p>
<p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
</div>
<div class="flex gap-8">
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="{{ route('landing') }}">Privacy Policy</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="{{ route('landing') }}">Terms of Service</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors opacity-80 hover:opacity-100" href="{{ route('login') }}">Contact Support</a>
</div>
</div>
</footer>
</main>
<!-- Contextual FAB - Restricted to Dashboard Use Cases -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-gradient-primary text-white rounded-full shadow-2xl flex items-center justify-center group active:scale-90 transition-all z-50">
<span class="material-symbols-outlined text-[28px]" data-icon="add_chart">add_chart</span>
<span class="absolute right-full mr-4 px-4 py-2 bg-primary text-white text-label-caps font-label-caps rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">Create Custom View</span>
</button>
@push('scripts')
<script>
// Inject real analytics data from API
(async () => {
    try {
        const res  = await fetch('{{ route('admin.analytics.data') }}');
        const data = await res.json();
        // Expose for any inline Chart.js code in the page
        window.analyticsData = data;
    } catch (e) { /* silently fail if API unavailable */ }
})();
</script>
@endpush
@endsection
