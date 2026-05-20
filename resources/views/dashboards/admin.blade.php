@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('body-class', 'bg-background text-on-background font-body-md min-h-screen')

@section('page-css', 'admin_analytics_dashboard.css')

@section('tailwind-config', 'tailwind-config-admin.js')

@section('content')
<!-- Sidebar -->
@include('partials.nav.admin-sidebar', ['activeNav' => 'dashboard'])

<main class="lg:ml-[280px] min-h-screen">
    <!-- Topbar -->
    <header class="glass-panel border-b border-[#1E293B] h-[64px] flex items-center justify-between px-6 lg:px-8 sticky top-0 z-40">
        <div class="flex items-center gap-3">
            <button type="button" class="lg:hidden p-2 rounded-xl text-[#64748B] hover:text-[#E2E8F0] hover:bg-[#263248] transition-all" onclick="document.getElementById('app-sidebar')?.classList.toggle('sidebar-mobile-open')">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div>
                <h1 class="text-[18px] font-bold text-[#E2E8F0]">Admin Dashboard</h1>
                <p class="text-[12px] text-[#475569] hidden sm:block">Platform overview</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.analytics') }}" class="btn-secondary py-2 px-4 text-[13px]">Analytics</a>
            <button onclick="toggleTheme()" class="p-2 rounded-xl text-[#64748B] hover:text-[#C4B5FD] hover:bg-[#1E293B] transition-all">
                <span class="material-symbols-outlined text-[20px]" data-theme-icon>light_mode</span>
            </button>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="btn-ghost py-2 px-4 text-[13px]">Sign Out</button>
            </form>
        </div>
    </header>

    <div class="pt-6 pb-12 px-6 lg:px-8 max-w-[1440px] mx-auto page-content">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-fade-in">
            <div class="glass-card kpi-card p-5">
                <div class="w-10 h-10 rounded-xl bg-[#1E1B4B] flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[#8B5CF6] text-[20px]">people</span>
                </div>
                <p class="text-[32px] font-extrabold text-[#E2E8F0]" data-counter="{{ $totalUsers }}">0</p>
                <p class="text-[12px] text-[#64748B] mt-1">Total Users</p>
            </div>
            <div class="glass-card kpi-card-cyan p-5">
                <div class="w-10 h-10 rounded-xl bg-[#164E63] flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[#06B6D4] text-[20px]">business_center</span>
                </div>
                <p class="text-[32px] font-extrabold text-[#E2E8F0]" data-counter="{{ $totalHr }}">0</p>
                <p class="text-[12px] text-[#64748B] mt-1">HR Accounts</p>
            </div>
            <div class="glass-card kpi-card-green p-5">
                <div class="w-10 h-10 rounded-xl bg-[#14532D] flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[#34D399] text-[20px]">work</span>
                </div>
                <p class="text-[32px] font-extrabold text-[#E2E8F0]" data-counter="{{ $activeJobs }}">0</p>
                <p class="text-[12px] text-[#64748B] mt-1">Active Jobs</p>
            </div>
            <div class="glass-card kpi-card-amber p-5">
                <div class="w-10 h-10 rounded-xl bg-[#451A03] flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[#FBBF24] text-[20px]">assignment</span>
                </div>
                <p class="text-[32px] font-extrabold text-[#E2E8F0]" data-counter="{{ $totalApplications }}">0</p>
                <p class="text-[12px] text-[#64748B] mt-1">Applications</p>
            </div>
        </div>

        <!-- Tables -->
        <div class="grid lg:grid-cols-2 gap-6 animate-fade-in-delay-1">
            <!-- Users table -->
            <section class="glass-card overflow-hidden">
                <div class="p-6 border-b border-[#334155] flex items-center justify-between">
                    <div>
                        <h2 class="text-[16px] font-bold text-[#E2E8F0]">All Users</h2>
                        <p class="text-[12px] text-[#64748B] mt-0.5">{{ $users->count() }} registered</p>
                    </div>
                    <span class="badge-violet">Candidates</span>
                </div>
                <div class="overflow-x-auto max-h-[360px] overflow-y-auto no-scrollbar">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $account)
                            <tr>
                                <td class="text-[13px] font-medium text-[#E2E8F0]">{{ $account->name }}</td>
                                <td class="text-[13px] text-[#64748B]">{{ $account->email }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-[13px] text-[#475569]">No users registered yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- HR table -->
            <section class="glass-card overflow-hidden">
                <div class="p-6 border-b border-[#334155] flex items-center justify-between">
                    <div>
                        <h2 class="text-[16px] font-bold text-[#E2E8F0]">HR Accounts</h2>
                        <p class="text-[12px] text-[#64748B] mt-0.5">{{ $hrs->count() }} registered</p>
                    </div>
                    <span class="badge-ai">Employers</span>
                </div>
                <div class="overflow-x-auto max-h-[360px] overflow-y-auto no-scrollbar">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hrs as $account)
                            <tr>
                                <td class="text-[13px] font-medium text-[#E2E8F0]">{{ $account->name }}</td>
                                <td class="text-[13px] text-[#64748B]">{{ $account->email }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-[13px] text-[#475569]">No HR accounts yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        @include('partials.nav.dashboard-footer')
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var counters = document.querySelectorAll('[data-counter]');
    counters.forEach(function (el) {
        var target = parseInt(el.getAttribute('data-counter'), 10) || 0;
        var duration = 600, start = 0, step = Math.ceil(target / (duration / 16));
        var timer = setInterval(function () {
            start += step;
            if (start >= target) { start = target; clearInterval(timer); }
            el.textContent = start;
        }, 16);
    });
});
</script>
@endsection
