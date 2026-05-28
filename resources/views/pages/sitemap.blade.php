@extends('layouts.app')

@section('title', 'Site Map')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
@include('partials.nav.public-header')

<main class="pt-28 pb-16 max-w-4xl mx-auto px-container-margin">
    <h1 class="font-headline-lg text-headline-lg text-primary mb-2">{{ config('app.name') }} — All Pages</h1>
    <p class="font-body-md text-on-surface-variant mb-10">Requires login for role-protected areas.</p>

    <div class="grid md:grid-cols-2 gap-8">
        <section class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant">
            <h2 class="font-title-md text-title-md text-secondary mb-4">Public</h2>
            <ul class="space-y-2 font-body-md">
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('landing') }}">Landing Page</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('login') }}">Login</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('register') }}">Register</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('suite.one') }}">Executive Suite 1</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('suite.two') }}">Executive Suite 2</a></li>
            </ul>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant">
            <h2 class="font-title-md text-title-md text-secondary mb-4">User (role: user)</h2>
            <ul class="space-y-2 font-body-md">
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('user.dashboard') }}">Dashboard</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('user.jobs.recommendations') }}">Job Recommendations</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('user.jobs.show', 1) }}">Job Details</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('user.resume.upload') }}">Resume Upload</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('user.resume.analytics') }}">Resume Analytics</a></li>
            </ul>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant">
            <h2 class="font-title-md text-title-md text-secondary mb-4">HR (role: hr)</h2>
            <ul class="space-y-2 font-body-md">
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('hr.dashboard') }}">Dashboard</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('hr.jobs.create') }}">Post a Job</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('hr.applicants') }}">Applicant Management</a></li>
            </ul>
        </section>

        <section class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant">
            <h2 class="font-title-md text-title-md text-secondary mb-4">Super Admin (role: admin)</h2>
            <ul class="space-y-2 font-body-md">
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                <li><a class="text-primary hover:text-secondary hover:underline" href="{{ route('admin.analytics') }}">Analytics UI</a></li>
            </ul>
        </section>
    </div>
</main>

@include('partials.nav.public-footer')
@endsection
