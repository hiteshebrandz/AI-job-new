@php
    $isHr = auth()->user()->isHr();
@endphp

@if ($isHr)
@extends('layouts.employer', ['activeNav' => 'settings'])

@section('title', 'Email Notification Settings')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'email_notification_settings.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('employer-main')
<div class="mb-10">
<h2 class="font-headline-lg text-headline-lg text-primary mb-2">Notification Preferences</h2>
<p class="font-body-md text-on-surface-variant max-w-2xl">Manage how and when you receive updates from the Elements HR platform.</p>
</div>
@include('partials.settings.notification-preferences')
@endsection

@else
@extends('layouts.candidate', ['activeNav' => 'settings'])

@section('title', 'Email Notification Settings')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'email_notification_settings.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('page-main')
<div class="mb-10">
<h2 class="font-headline-lg text-headline-lg text-primary mb-2">Notification Preferences</h2>
<p class="font-body-md text-on-surface-variant max-w-2xl">Manage how and when you receive updates from the Elements HR platform. Customize alerts for your job search.</p>
</div>
@include('partials.settings.notification-preferences')
@endsection
@endif
