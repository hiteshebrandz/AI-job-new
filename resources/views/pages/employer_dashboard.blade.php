@extends('layouts.app')

@section('title', 'Employer Dashboard')

@section('body-class', 'bg-surface text-on-surface')

@section('page-css', 'employer_dashboard.css')

@section('tailwind-config', 'tailwind-config-employer.js')

@section('content')
<!-- SideNavBar Shell -->
@include('partials.nav.employer-sidebar')
<!-- TopNavBar Shell -->
<header class="fixed top-0 right-0 w-[calc(100%-280px)] h-16 z-40 bg-surface/80 backdrop-blur-lg border-b border-outline-variant flex justify-between items-center px-gutter">
<div class="flex items-center bg-surface-container-low px-4 py-2 rounded-xl w-96 focus-within:ring-2 focus-within:ring-secondary/50 transition-all">
<span class="material-symbols-outlined text-on-surface-variant mr-2" data-icon="search">search</span>
<input class="bg-transparent border-none focus:ring-0 text-body-sm w-full placeholder:text-on-surface-variant/50" placeholder="Search jobs or candidates..." type="text"/>
</div>
<div class="flex items-center gap-6">
<button class="relative text-on-surface-variant hover:text-secondary transition-colors">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
<span class="absolute top-0 right-0 w-2 h-2 bg-error rounded-full"></span>
</button>
<div class="h-8 w-[1px] bg-outline-variant"></div>
<p class="font-label-caps text-label-caps text-secondary font-bold">{{ strtoupper(config('app.name')) }}</p>
</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-[280px] pt-24 pb-12 px-container-margin max-w-[1440px]">
<!-- Header Section -->
<header class="mb-10">
<h2 class="font-headline-lg text-headline-lg text-primary mb-2">Employer Dashboard</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Good morning, Marcus. Here is your hiring overview for today.</p>
</header>
<!-- Stats Grid (High Contrast Cards) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter mb-10">
<div class="bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all border-l-4 border-l-secondary">
<div class="flex justify-between items-start mb-4">
<span class="material-symbols-outlined text-secondary" data-icon="work">work</span>
<span class="text-emerald-600 font-bold font-label-caps">+2 this week</span>
</div>
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest mb-1">Active Jobs</p>
<h3 class="font-display-lg text-headline-lg text-primary">12</h3>
</div>
<div class="bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all border-l-4 border-l-secondary">
<div class="flex justify-between items-start mb-4">
<span class="material-symbols-outlined text-secondary" data-icon="person_add">person_add</span>
<span class="text-secondary font-bold font-label-caps">48 New</span>
</div>
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest mb-1">New Applicants</p>
<h3 class="font-display-lg text-headline-lg text-primary">156</h3>
</div>
<div class="bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all border-l-4 border-l-secondary">
<div class="flex justify-between items-start mb-4">
<span class="material-symbols-outlined text-secondary" data-icon="calendar_month">calendar_month</span>
<span class="text-on-surface-variant font-bold font-label-caps">Today: 4</span>
</div>
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest mb-1">Interviews</p>
<h3 class="font-display-lg text-headline-lg text-primary">24</h3>
</div>
<div class="bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] hover:shadow-lg transition-all border-l-4 border-l-secondary">
<div class="flex justify-between items-start mb-4">
<span class="material-symbols-outlined text-secondary" data-icon="verified">verified</span>
<span class="text-secondary font-bold font-label-caps">85% Accept Rate</span>
</div>
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest mb-1">Offers Sent</p>
<h3 class="font-display-lg text-headline-lg text-primary">08</h3>
</div>
</div>
<!-- Bento Grid Section -->
<div class="grid grid-cols-12 gap-gutter">
<!-- Applicant Pipeline Chart (Large) -->
<div class="col-span-12 lg:col-span-8 bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant shadow-sm">
<div class="flex justify-between items-center mb-8">
<h4 class="font-title-md text-title-md text-primary">Applicant Pipeline</h4>
<div class="flex gap-2">
<button class="px-3 py-1 bg-surface-container-high rounded-full font-label-caps text-[10px]">ALL JOBS</button>
<button class="px-3 py-1 border border-outline-variant rounded-full font-label-caps text-[10px]">QUARTERLY</button>
</div>
</div>
<div class="flex items-end justify-between h-64 gap-4 px-4">
<div class="flex flex-col items-center flex-1 group">
<div class="w-full bg-secondary/10 rounded-t-lg relative flex items-end justify-center transition-all h-[100%] group-hover:bg-secondary/20">
<div class="w-full bg-secondary h-[40%] rounded-t-lg"></div>
<span class="absolute -top-8 font-bold text-secondary">412</span>
</div>
<p class="font-label-caps text-label-caps mt-4 text-on-surface-variant">Applied</p>
</div>
<div class="flex flex-col items-center flex-1 group">
<div class="w-full bg-secondary/10 rounded-t-lg relative flex items-end justify-center transition-all h-[65%] group-hover:bg-secondary/20">
<div class="w-full bg-secondary h-[35%] rounded-t-lg"></div>
<span class="absolute -top-8 font-bold text-secondary">248</span>
</div>
<p class="font-label-caps text-label-caps mt-4 text-on-surface-variant">Screen</p>
</div>
<div class="flex flex-col items-center flex-1 group">
<div class="w-full bg-secondary/10 rounded-t-lg relative flex items-end justify-center transition-all h-[40%] group-hover:bg-secondary/20">
<div class="w-full bg-secondary h-[30%] rounded-t-lg"></div>
<span class="absolute -top-8 font-bold text-secondary">112</span>
</div>
<p class="font-label-caps text-label-caps mt-4 text-on-surface-variant">Interview</p>
</div>
<div class="flex flex-col items-center flex-1 group">
<div class="w-full bg-secondary/10 rounded-t-lg relative flex items-end justify-center transition-all h-[15%] group-hover:bg-secondary/20">
<div class="w-full bg-secondary h-[60%] rounded-t-lg"></div>
<span class="absolute -top-8 font-bold text-secondary">32</span>
</div>
<p class="font-label-caps text-label-caps mt-4 text-on-surface-variant">Offer</p>
</div>
</div>
</div>
<!-- Urgent Hires (Sidebar Column) -->
<div class="col-span-12 lg:col-span-4 flex flex-col gap-gutter">
<div class="bg-primary text-white p-card-padding rounded-xl shadow-lg relative overflow-hidden">
<div class="relative z-10">
<h4 class="font-title-md text-title-md mb-2">Urgent Hires</h4>
<p class="font-body-sm text-body-sm opacity-70 mb-6">Positions requiring immediate attention</p>
<div class="space-y-6">
<div>
<div class="flex justify-between mb-2">
<span class="font-body-md font-semibold">Sr. Product Designer</span>
<span class="font-body-sm opacity-80">85% Vol</span>
</div>
<div class="w-full h-2 bg-white/20 rounded-full overflow-hidden">
<div class="h-full bg-white w-[85%] rounded-full"></div>
</div>
</div>
<div>
<div class="flex justify-between mb-2">
<span class="font-body-md font-semibold">Lead DevOps Engineer</span>
<span class="font-body-sm opacity-80">42% Vol</span>
</div>
<div class="w-full h-2 bg-white/20 rounded-full overflow-hidden">
<div class="h-full bg-white w-[42%] rounded-full"></div>
</div>
</div>
<div>
<div class="flex justify-between mb-2">
<span class="font-body-md font-semibold">Recruiting Manager</span>
<span class="font-body-sm opacity-80">68% Vol</span>
</div>
<div class="w-full h-2 bg-white/20 rounded-full overflow-hidden">
<div class="h-full bg-white w-[68%] rounded-full"></div>
</div>
</div>
</div>
<button class="mt-8 w-full py-3 bg-white text-primary font-bold rounded-xl active:scale-95 transition-all">View All Priorities</button>
</div>
<div class="absolute -right-10 -bottom-10 w-40 h-40 bg-secondary/30 blur-3xl rounded-full"></div>
</div>
<div class="bg-surface-container-lowest p-card-padding rounded-xl border border-outline-variant flex-1">
<h4 class="font-title-md text-title-md text-primary mb-4">Quick Actions</h4>
<div class="grid grid-cols-2 gap-3">
<a href="{{ route('hr.jobs.create') }}" class="flex flex-col items-center justify-center p-4 border border-outline-variant rounded-xl hover:bg-secondary/5 hover:border-secondary transition-all group">
<span class="material-symbols-outlined text-secondary mb-2 group-hover:scale-110 transition-transform" data-icon="post_add">post_add</span>
<span class="font-label-caps text-center">Post Job</span>
</a>
<a href="{{ route('hr.applicants') }}" class="flex flex-col items-center justify-center p-4 border border-outline-variant rounded-xl hover:bg-secondary/5 hover:border-secondary transition-all group">
<span class="material-symbols-outlined text-secondary mb-2 group-hover:scale-110 transition-transform" data-icon="group_add">group_add</span>
<span class="font-label-caps text-center">View Applicants</span>
</a>
</div>
</div>
</div>
</div>
<!-- Recent Activity Table -->
<section class="mt-10">
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<div class="p-card-padding border-b border-outline-variant flex justify-between items-center">
<h4 class="font-title-md text-title-md text-primary">Recent Applicant Activity</h4>
<a class="text-secondary font-bold font-label-caps hover:underline" href="{{ route('hr.applicants') }}">VIEW ALL</a>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead>
<tr class="bg-surface-container-low">
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">CANDIDATE</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">POSITION</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">STAGE</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">MATCH SCORE</th>
<th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant">ACTION</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<tr class="hover:bg-surface-container-high transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<img alt="Sarah Jenkins" class="w-10 h-10 rounded-full object-cover" data-alt="A polished professional headshot of a woman with a bright smile, wearing a professional charcoal grey blazer. The background is a brightly lit, modern office space with subtle greenery and glass partitions. The lighting is soft and flattering, emphasizing a premium and corporate-friendly environment." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBF5PhLPiD821Ns8EJSMRoXibbka6Cfk872MNXSnBuYNCYV_ngUyJSjLh4ZimBHPHtbkcEf1rbYRdakYUSA1UZ_BZvZgxpRMMHPl_Fv9wGYLUGBaJyqo1dwFPl_GJdtEuPoFVDqEOm8LmMqPw2Wdt8GWJk1vgad9Qa9X-805CN7U526pQ5QfZlYPyt1rlyn42tnBQPsAxfNTS-TA2iXqFRy4g6rs58V1ttySPObW7JTEUlNxquencxZCpm5gqMiwzxcnOzitYtJEni4"/>
<div>
<p class="font-body-md font-semibold">Sarah Jenkins</p>
<p class="font-body-sm text-on-surface-variant">Applied 2h ago</p>
</div>
</div>
</td>
<td class="px-6 py-4 font-body-sm">Senior Product Designer</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-secondary/10 text-secondary rounded-full font-label-caps text-[10px]">Applied</span>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-12 h-1.5 bg-surface-container-high rounded-full">
<div class="h-full progress-gradient w-[94%] rounded-full"></div>
</div>
<span class="font-bold text-secondary text-body-sm">94%</span>
</div>
</td>
<td class="px-6 py-4">
<button class="material-symbols-outlined text-on-surface-variant hover:text-secondary" data-icon="more_vert">more_vert</button>
</td>
</tr>
<tr class="hover:bg-surface-container-high transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<img alt="David Miller" class="w-10 h-10 rounded-full object-cover" data-alt="A headshot of a professional man in his late 30s with short hair and glasses, wearing a light blue dress shirt. He is looking directly at the camera with a friendly expression. The background is a clean, minimalist executive office setting with natural light coming from a large window. The aesthetic is high-end and modern." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCSfu1sDWJfdBczHrAM1yxAAV3ZcBl4NiHXYTGRGGY9zJE4aFFRyLmbwyqa_kb91xT99heDl68PljoZmTJyADkudg_kyAa5x4YwtdqrivGXQqCiclXDcKPFbxpbISn3ibOS9wvVX4SFcAxj0h1cKlfGk3SS1WZZtaxlstAkp7f7rvvzeDeODk8TENMcyoxTE0fxLFPxoIUuSP4OIFVYwOVjGs6Pfgtzt96abdowQcaipg0xYT9DEHqdFKPKEcd_XGb1q9iziNVGBdOj"/>
<div>
<p class="font-body-md font-semibold">David Miller</p>
<p class="font-body-sm text-on-surface-variant">Applied 5h ago</p>
</div>
</div>
</td>
<td class="px-6 py-4 font-body-sm">DevOps Architect</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-secondary/10 text-secondary rounded-full font-label-caps text-[10px]">Screening</span>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-12 h-1.5 bg-surface-container-high rounded-full">
<div class="h-full progress-gradient w-[88%] rounded-full"></div>
</div>
<span class="font-bold text-secondary text-body-sm">88%</span>
</div>
</td>
<td class="px-6 py-4">
<button class="material-symbols-outlined text-on-surface-variant hover:text-secondary" data-icon="more_vert">more_vert</button>
</td>
</tr>
<tr class="hover:bg-surface-container-high transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<img alt="Aisha Khan" class="w-10 h-10 rounded-full object-cover" data-alt="A professional portrait of a woman of South Asian descent with dark hair, wearing a white blouse and a beige blazer. She is smiling confidently in a contemporary workspace with bright, high-key lighting. The environment is clean and sophisticated, representing a high-level executive setting with glass and metal accents." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBR-NbW_ZeEZlPB0uu0MSRu93TPQLwgi1z1KhktJR1TRH9CCyyaRQu_Q8jcT5_gyQPyR7l65X-rfCp16XNmUbEVYuEV273djHKO7lW0l7TZLHytDOWtwfb2a2w_wobykO5QvhVOOTSWLCA8HdCdFSQw0frqgnrdQYxZekDQox2DQInCCZLVIfcFZMolS58_PExZK4d72A3r70WXAciTT7qBAkhzYh3HiF1zPy_pqwpAtNCGJVvs4hwu174jOrA1djkZWujiULNaHEL_"/>
<div>
<p class="font-body-md font-semibold">Aisha Khan</p>
<p class="font-body-sm text-on-surface-variant">Applied 1d ago</p>
</div>
</div>
</td>
<td class="px-6 py-4 font-body-sm">Lead Front-end Dev</td>
<td class="px-6 py-4">
<span class="px-3 py-1 bg-secondary/10 text-secondary rounded-full font-label-caps text-[10px]">Applied</span>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<div class="w-12 h-1.5 bg-surface-container-high rounded-full">
<div class="h-full progress-gradient w-[76%] rounded-full"></div>
</div>
<span class="font-bold text-secondary text-body-sm">76%</span>
</div>
</td>
<td class="px-6 py-4">
<button class="material-symbols-outlined text-on-surface-variant hover:text-secondary" data-icon="more_vert">more_vert</button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</section>
</main>
<!-- Footer Shell -->
<footer class="ml-[280px] border-t border-outline-variant bg-surface py-8">
<div class="max-w-7xl mx-auto px-container-margin flex flex-col md:flex-row justify-between items-center opacity-80 hover:opacity-100 transition-opacity">
<p class="font-body-sm text-body-sm text-on-surface-variant mb-4 md:mb-0">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
<div class="flex gap-6">
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Terms of Service</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('login') }}">Contact Support</a>
</div>
</div>
</footer>
<!-- FAB for Create New -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-secondary text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-50">
<span class="material-symbols-outlined" data-icon="add">add</span>
</button>
@endsection
