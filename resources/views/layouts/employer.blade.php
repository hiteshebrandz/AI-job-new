@extends('layouts.app')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stack('employer-head')
@endpush

@section('content')
<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" class="sidebar-overlay lg:hidden" onclick="closeSidebar()"></div>

@include('partials.nav.employer-sidebar', ['activeNav' => $activeNav ?? ''])

<main class="lg:ml-[280px] min-h-screen transition-all duration-300" id="main-content">
    @include('partials.nav.candidate-topbar')
    @hasSection('employer-main-full')
        @yield('employer-main-full')
    @else
        <div class="pt-[80px] pb-12 px-6 lg:px-8 max-w-[1440px] mx-auto page-content">
            @yield('employer-main')
        </div>
    @endif
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/candidate-topbar.js') }}"></script>
<script src="{{ asset('js/hr-messages-badge.js') }}"></script>
<script>
function openSidebar() {
    document.getElementById('app-sidebar')?.classList.add('sidebar-mobile-open');
    document.getElementById('sidebar-overlay')?.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('app-sidebar')?.classList.remove('sidebar-mobile-open');
    document.getElementById('sidebar-overlay')?.classList.remove('active');
    document.body.style.overflow = '';
}
</script>
@stack('employer-scripts')
@endpush
