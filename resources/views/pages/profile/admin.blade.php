@extends('layouts.app')

@section('title', 'Account Settings')

@section('body-class', 'bg-background text-on-background font-body-md min-h-screen')

@section('page-css', 'profile_settings.css')

@section('tailwind-config', 'tailwind-config-admin.js')

@section('content')
    @include('partials.nav.admin-sidebar', ['activeNav' => 'profile'])
    <main class="lg:ml-[280px] min-h-screen">
        <header class="glass-panel border-b h-[64px] flex items-center justify-between px-6 lg:px-8 sticky top-0 z-40" style="border-color: var(--border-default);">
            <h1 class="text-lg font-bold" style="color: var(--text-primary);">Account Settings</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-secondary hover:underline">← Dashboard</a>
        </header>
        <div class="pt-6 pb-12 px-6 lg:px-8 max-w-[1440px] mx-auto page-content">
            @include('partials.profile.settings-form')
        </div>
    </main>
@endsection
