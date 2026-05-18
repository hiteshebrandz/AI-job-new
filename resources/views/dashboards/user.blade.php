@extends('layouts.candidate', ['activeNav' => 'dashboard'])

@section('title', 'User Dashboard')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'candidate_dashboard.css')

@section('tailwind-config', 'tailwind-config-candidate.js')

@section('page-main')
<div class="mb-8">
    <h1 class="font-headline-lg text-headline-lg text-primary">Welcome, {{ $user->name }}</h1>
    <p class="font-body-md text-on-surface-variant">Your candidate dashboard</p>
</div>

<section class="grid md:grid-cols-2 gap-6">
    <a href="{{ route('user.resume.upload') }}" class="glass-card p-8 rounded-2xl hover:shadow-lg transition-all border border-outline-variant group">
        <span class="material-symbols-outlined text-secondary text-4xl mb-4 group-hover:scale-110 transition-transform">upload_file</span>
        <h2 class="font-title-md text-title-md text-primary mb-2">Upload Resume</h2>
        <p class="font-body-sm text-on-surface-variant">Parse and optimize your resume with AI.</p>
    </a>
    <a href="{{ route('user.jobs.recommendations') }}" class="glass-card p-8 rounded-2xl hover:shadow-lg transition-all border border-outline-variant group">
        <span class="material-symbols-outlined text-secondary text-4xl mb-4 group-hover:scale-110 transition-transform">work</span>
        <h2 class="font-title-md text-title-md text-primary mb-2">Matching Jobs</h2>
        <p class="font-body-sm text-on-surface-variant">Browse AI-recommended opportunities.</p>
    </a>
</section>
@endsection
