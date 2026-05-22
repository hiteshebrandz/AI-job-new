@extends('layouts.employer', ['activeNav' => 'profile'])

@section('title', 'Account Settings')

@section('body-class', 'bg-background text-on-surface font-body-md min-h-screen')

@section('page-css', 'profile_settings.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('employer-main')
    @include('partials.profile.settings-form')
    @include('partials.nav.dashboard-footer')
@endsection
