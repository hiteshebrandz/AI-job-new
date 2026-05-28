@php
    $isHr = auth()->user()->isHr();
@endphp

@if ($isHr)
@extends('layouts.employer', ['activeNav' => 'settings'])
@section('title', 'Notification Settings')
@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')
@section('page-css', 'email_notification_settings.css')
@section('tailwind-config', 'tailwind-config-default.js')

@section('employer-main')
<div class="mb-8 animate-fade-in">
    <div class="flex items-center gap-3 mb-2">
        <span class="badge-violet text-[11px]">Settings</span>
    </div>
    <h2 class="text-[28px] font-extrabold text-on-surface">Notification Preferences</h2>
    <p class="text-[14px] text-on-surface-variant mt-1 max-w-2xl">Manage how and when you receive updates from the {{ config('app.name') }} platform.</p>
</div>
@include('partials.settings.notification-preferences')
@include('partials.nav.dashboard-footer')
@endsection

@else
@extends('layouts.candidate', ['activeNav' => 'settings'])
@section('title', 'Notification Settings')
@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')
@section('page-css', 'email_notification_settings.css')
@section('tailwind-config', 'tailwind-config-default.js')

@section('page-main')
<div class="mb-8 animate-fade-in">
    <div class="flex items-center gap-3 mb-2">
        <span class="badge-violet text-[11px]">Settings</span>
    </div>
    <h2 class="text-[28px] font-extrabold text-on-surface">Notification Preferences</h2>
    <p class="text-[14px] text-on-surface-variant mt-1 max-w-2xl">Manage how and when you receive updates from the {{ config('app.name') }} platform. Customize alerts for your job search.</p>
</div>
@include('partials.settings.notification-preferences')
@include('partials.nav.dashboard-footer')
@endsection
@endif
