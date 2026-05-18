@extends('layouts.candidate', ['activeNav' => 'analytics'])

@section('title', 'Resume Analytics')

@section('body-class', 'bg-background text-on-surface font-body-md overflow-x-hidden')

@section('page-css', 'resume_analytics_dashboard.css')

@section('tailwind-config', 'tailwind-config-resume-analytics.js')

@section('page-main')
<div class="space-y-8">
<!-- Candidate Header Section -->
<section class="flex flex-col md:flex-row items-end justify-between gap-6">
<div class="flex items-start gap-8">
<div class="relative">
<img alt="Elena Rodriguez" class="w-32 h-32 rounded-2xl object-cover shadow-lg border-2 border-white" data-alt="A detailed portrait of a sophisticated female professional with a warm smile, wearing a designer charcoal blazer. The background is a bright, minimalist studio space with soft natural lighting coming from the side. The image exudes professional intelligence, confidence, and high-end corporate style." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDURh9dGMyIHKGciLfSCCNfT7xUrt8fjGx7Lt75kxoxF3VVB9xmv2bxM0OHskNIxvAeF1U6L6J55kmBNbpuC-TRQvKcNIDtr3FcGR7Za7OCXkZaMzk6mvBK0U6CqZoKgYPxEEcDrkM_GzgM6iqSBDIy6xfDTiJ1jASSfwq1BLWOLxHG_EYMfWtI5Oe6RWEVF08apwH6TUwXb3xyDu7hOjTIYMV32CRGOBIhQR6LtRdAcfEKMzYAnKhi61dCWR2fWBHcizyyHeK7-b-l"/>
<div class="absolute -bottom-3 -right-3 bg-secondary text-white p-2 rounded-xl shadow-lg flex items-center gap-1">
<span class="font-headline-lg text-[18px]">94</span>
<span class="font-label-caps text-[8px] leading-tight">MATCH<br/>INDEX</span>
</div>
</div>
<div class="space-y-1">
<div class="flex items-center gap-3">
<h2 class="font-headline-lg text-headline-lg">Elena Rodriguez</h2>
<span class="bg-secondary/10 text-secondary px-3 py-1 rounded-full font-label-caps text-[10px]">PREMIUM TALENT</span>
</div>
<p class="font-title-md text-title-md text-on-surface-variant">Senior Product Strategist • 8+ Years Exp.</p>
<div class="flex gap-4 pt-2">
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[18px]" data-icon="location_on">location_on</span>
<span class="font-body-sm text-body-sm">London, UK</span>
</div>
<div class="flex items-center gap-1 text-on-surface-variant">
<span class="material-symbols-outlined text-[18px]" data-icon="mail">mail</span>
<span class="font-body-sm text-body-sm">e.rodriguez@@domain.com</span>
</div>
</div>
</div>
</div>
<div class="flex gap-3">
<button class="bg-surface border border-outline-variant px-6 py-2.5 rounded-xl font-title-md text-sm hover:bg-surface-container-high transition-all">Download PDF</button>
<button class="bg-gradient-to-r from-secondary to-purple-600 text-white px-6 py-2.5 rounded-xl font-title-md text-sm shadow-md hover:scale-[1.02] transition-all">Schedule Interview</button>
</div>
</section>
<!-- Bento Grid Dashboard -->
<div class="grid grid-cols-12 gap-6">
<!-- Radar Chart: Skill Gap Analysis -->
<div class="col-span-12 lg:col-span-5 glass-card p-card-padding rounded-xl shadow-sm">
<div class="flex items-center justify-between mb-8">
<div>
<h3 class="font-title-md text-title-md">Skill Gap Analysis</h3>
<p class="font-label-caps text-label-caps text-on-surface-variant opacity-60">CANDIDATE VS. ROLE REQUIREMENTS</p>
</div>
<span class="material-symbols-outlined text-secondary" data-icon="info_outline">help_outline</span>
</div>
<div class="relative h-64 flex items-center justify-center radar-grid rounded-lg border border-outline-variant/20 overflow-hidden">
<!-- Simulated Radar Chart Visualization -->
<div class="absolute inset-0 flex items-center justify-center">
<div class="w-48 h-48 border border-outline/30 rounded-full flex items-center justify-center">
<div class="w-32 h-32 border border-outline/30 rounded-full flex items-center justify-center">
<div class="w-16 h-16 border border-outline/30 rounded-full"></div>
</div>
</div>
<!-- Skill Polygon (Simulated with Clip-path/Divs) -->
<div class="absolute w-40 h-40 bg-secondary/20 border-2 border-secondary" style="clip-path: polygon(50% 0%, 90% 20%, 100% 60%, 75% 100%, 25% 100%, 0% 60%, 10% 20%);"></div>
<!-- Requirement Polygon -->
<div class="absolute w-44 h-44 border-2 border-dashed border-on-surface-variant/40" style="clip-path: polygon(50% 5%, 85% 25%, 95% 65%, 70% 95%, 30% 95%, 5% 65%, 15% 25%);"></div>
</div>
<!-- Skill Labels -->
<span class="absolute top-4 font-label-caps text-[10px]">Product Vision</span>
<span class="absolute right-4 top-24 font-label-caps text-[10px]">Data Analytics</span>
<span class="absolute right-12 bottom-4 font-label-caps text-[10px]">UX Design</span>
<span class="absolute left-12 bottom-4 font-label-caps text-[10px]">Stakeholder Mgmt</span>
<span class="absolute left-4 top-24 font-label-caps text-[10px]">Agile Methodology</span>
</div>
<div class="mt-6 flex justify-around">
<div class="flex items-center gap-2">
<span class="w-3 h-3 bg-secondary rounded-sm"></span>
<span class="font-label-caps text-xs">Elena's Profile</span>
</div>
<div class="flex items-center gap-2">
<span class="w-3 h-3 border border-dashed border-on-surface-variant rounded-sm"></span>
<span class="font-label-caps text-xs">Job Benchmark</span>
</div>
</div>
</div>
<!-- Career Growth Timeline -->
<div class="col-span-12 lg:col-span-7 glass-card p-card-padding rounded-xl shadow-sm">
<div class="flex items-center justify-between mb-8">
<div>
<h3 class="font-title-md text-title-md">Career Growth Trajectory</h3>
<p class="font-label-caps text-label-caps text-on-surface-variant opacity-60">88% VELOCITY RATING</p>
</div>
<div class="flex gap-2">
<button class="bg-surface-container px-3 py-1 rounded-lg font-label-caps text-[10px] border border-outline-variant">VIEW FULL LOG</button>
</div>
</div>
<div class="relative space-y-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[2px] before:bg-outline-variant">
<!-- Timeline Item -->
<div class="relative pl-10">
<div class="absolute left-0 top-1.5 w-6 h-6 rounded-full bg-secondary ring-4 ring-white flex items-center justify-center">
<span class="material-symbols-outlined text-white text-[14px]" data-icon="star" data-weight="fill">star</span>
</div>
<div class="flex justify-between items-start">
<div>
<h4 class="font-title-md text-sm font-bold">Principal Product Lead</h4>
<p class="font-body-sm text-xs text-on-surface-variant">Global FinTech Corp • 2021 - Present</p>
</div>
<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-label-caps text-[9px]">PROMOTION</span>
</div>
<p class="mt-2 font-body-sm text-xs leading-relaxed text-on-surface-variant/80">Scaling cross-functional teams from 5 to 45. Managed a $12M ARR product portfolio with 24% YoY growth.</p>
</div>
<!-- Timeline Item -->
<div class="relative pl-10">
<div class="absolute left-0 top-1.5 w-6 h-6 rounded-full bg-surface-container-highest border-2 border-secondary ring-4 ring-white"></div>
<div class="flex justify-between items-start">
<div>
<h4 class="font-title-md text-sm font-bold">Senior Product Manager</h4>
<p class="font-body-sm text-xs text-on-surface-variant">NextGen AI Systems • 2018 - 2021</p>
</div>
</div>
<p class="mt-2 font-body-sm text-xs leading-relaxed text-on-surface-variant/80">Led the launch of 3 core features. Decreased churn rate by 15% through data-driven UX improvements.</p>
</div>
<!-- Timeline Item -->
<div class="relative pl-10 opacity-60">
<div class="absolute left-0 top-1.5 w-6 h-6 rounded-full bg-surface-container-highest border-2 border-outline ring-4 ring-white"></div>
<div>
<h4 class="font-title-md text-sm font-bold">Product Associate</h4>
<p class="font-body-sm text-xs text-on-surface-variant">Startup X • 2016 - 2018</p>
</div>
</div>
</div>
</div>
<!-- Educational Prestige & NLP Sentiment -->
<div class="col-span-12 lg:col-span-4 glass-card p-card-padding rounded-xl shadow-sm flex flex-col justify-between">
<div>
<div class="mb-6">
<h3 class="font-title-md text-title-md">Educational Prestige</h3>
<p class="font-label-caps text-label-caps text-on-surface-variant opacity-60">TOP-TIER ACADEMIC PROFILE</p>
</div>
<div class="space-y-4">
<div class="flex items-center gap-4 bg-surface-container-low p-3 rounded-xl border border-secondary/10">
<div class="w-12 h-12 flex-shrink-0 bg-white rounded-lg flex items-center justify-center shadow-sm">
<span class="material-symbols-outlined text-secondary" data-icon="school">school</span>
</div>
<div>
<h4 class="font-title-md text-sm font-bold">Stanford University</h4>
<p class="font-body-sm text-xs">M.S. Computer Science</p>
<p class="font-label-caps text-[10px] text-secondary mt-1">IVY LEAGUE EQUIVALENT</p>
</div>
</div>
<div class="flex items-center gap-4 bg-surface-container-low p-3 rounded-xl border border-outline-variant/30">
<div class="w-12 h-12 flex-shrink-0 bg-white rounded-lg flex items-center justify-center shadow-sm">
<span class="material-symbols-outlined text-on-surface-variant" data-icon="history_edu">history_edu</span>
</div>
<div>
<h4 class="font-title-md text-sm font-bold">UC Berkeley</h4>
<p class="font-body-sm text-xs">B.A. Economics</p>
</div>
</div>
</div>
</div>
<div class="mt-8 pt-6 border-t border-outline-variant/30">
<div class="flex items-center justify-between mb-2">
<span class="font-label-caps text-xs">ACADEMIC EXCELLENCE</span>
<span class="font-bold text-secondary">9.4/10</span>
</div>
<div class="w-full bg-surface-container h-1.5 rounded-full overflow-hidden">
<div class="h-full bg-secondary w-[94%] rounded-full"></div>
</div>
</div>
</div>
<!-- NLP Sentiment & Soft Skills -->
<div class="col-span-12 lg:col-span-8 glass-card p-card-padding rounded-xl shadow-sm relative overflow-hidden">
<div class="absolute -right-12 -top-12 w-48 h-48 bg-secondary/5 rounded-full blur-3xl"></div>
<div class="mb-8">
<h3 class="font-title-md text-title-md">NLP 'Intelligence' Analysis</h3>
<p class="font-label-caps text-label-caps text-on-surface-variant opacity-60">AI-EXTRACTED SENTIMENT &amp; SOFT SKILLS</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div class="space-y-6">
<div>
<div class="flex justify-between items-center mb-2">
<span class="font-title-md text-xs">Leadership Sentiment</span>
<span class="text-secondary font-bold">92%</span>
</div>
<div class="w-full bg-surface-container h-2 rounded-full flex gap-1">
<div class="h-full bg-secondary rounded-l-full w-[92%]"></div>
<div class="h-full bg-outline-variant/30 rounded-r-full w-[8%]"></div>
</div>
<p class="text-[10px] mt-2 text-on-surface-variant italic">"Keywords suggest high levels of empathy and strategic decisiveness."</p>
</div>
<div>
<div class="flex justify-between items-center mb-2">
<span class="font-title-md text-xs">Adaptability Score</span>
<span class="text-secondary font-bold">85%</span>
</div>
<div class="w-full bg-surface-container h-2 rounded-full flex gap-1">
<div class="h-full bg-secondary rounded-l-full w-[85%]"></div>
<div class="h-full bg-outline-variant/30 rounded-r-full w-[15%]"></div>
</div>
</div>
</div>
<div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/20">
<h4 class="font-label-caps text-xs mb-4 text-secondary">TOP SOFT SKILL CLUSTERS</h4>
<div class="flex flex-wrap gap-2">
<span class="bg-white border border-outline-variant/50 px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm">Emotional Intelligence</span>
<span class="bg-white border border-outline-variant/50 px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm">Critical Thinking</span>
<span class="bg-white border border-outline-variant/50 px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm">Conflict Resolution</span>
<span class="bg-white border border-outline-variant/50 px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm">Narrative Storytelling</span>
<span class="bg-white border border-outline-variant/50 px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm">Growth Mindset</span>
<span class="bg-white border border-outline-variant/50 px-3 py-1.5 rounded-lg text-xs font-medium shadow-sm">Influencing</span>
</div>
</div>
</div>
<div class="mt-8 bg-primary-container text-on-primary-container p-4 rounded-xl flex items-start gap-4">
<span class="material-symbols-outlined text-secondary" data-icon="psychology">psychology</span>
<div>
<p class="font-title-md text-sm text-on-primary font-bold">Executive Insight Summary</p>
<p class="font-body-sm text-xs mt-1 leading-relaxed opacity-80">Elena demonstrates a rare combination of technical rigor and high emotional intelligence. Her career trajectory shows consistent upward movement with increasing complexity in stakeholder management. Recommended for High-Level Strategy roles.</p>
</div>
</div>
</div>
</div>
</div>
<!-- Footer -->
<footer class="w-full py-8 mt-12 bg-surface dark:bg-inverse-surface border-t border-outline-variant">
<div class="flex flex-col md:flex-row justify-between items-center px-8 max-w-7xl mx-auto gap-4">
<p class="font-body-sm text-body-sm text-on-surface-variant">© 2024 Elements HR Services. All rights reserved.</p>
<div class="flex gap-6">
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Terms of Service</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('login') }}">Contact Support</a>
</div>
</div>
</footer>
@endsection
