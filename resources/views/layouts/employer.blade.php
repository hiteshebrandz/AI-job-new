@extends('layouts.app')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stack('employer-head')
@endpush

@section('content')
@include('partials.nav.employer-sidebar', ['activeNav' => $activeNav ?? ''])
<main class="ml-[280px] min-h-screen">
@include('partials.nav.candidate-topbar')
@hasSection('employer-main-full')
@yield('employer-main-full')
@else
<div class="pt-24 pb-12 px-8 max-w-[1440px] mx-auto">
@yield('employer-main')
</div>
@endif
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/candidate-topbar.js') }}"></script>
@stack('employer-scripts')
@endpush
