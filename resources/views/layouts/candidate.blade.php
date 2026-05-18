@extends('layouts.app')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stack('candidate-head')
@endpush

@section('content')
@include('partials.nav.candidate-sidebar', ['activeNav' => $activeNav ?? ''])
<main class="ml-[280px] min-h-screen">
@include('partials.nav.candidate-topbar')
@hasSection('page-main-full')
@yield('page-main-full')
@else
<div class="pt-24 pb-12 px-8 max-w-[1440px] mx-auto">
@yield('page-main')
</div>
@endif
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/candidate-topbar.js') }}"></script>
@stack('page-scripts')
@endpush
