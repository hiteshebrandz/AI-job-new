@extends('layouts.app')

@push('head-scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stack('candidate-head')
@endpush

@section('content')
<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" class="sidebar-overlay lg:hidden" onclick="closeSidebar()"></div>

@include('partials.nav.candidate-sidebar', ['activeNav' => $activeNav ?? ''])

<main class="lg:ml-[280px] min-h-screen transition-all duration-300" id="main-content">
    @include('partials.nav.candidate-topbar')
    @hasSection('page-main-full')
        @yield('page-main-full')
    @else
        <div class="pt-[80px] pb-12 px-6 lg:px-8 max-w-[1440px] mx-auto page-content">
            @yield('page-main')
        </div>
    @endif
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/candidate-topbar.js') }}"></script>
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
@stack('page-scripts')
@endpush
