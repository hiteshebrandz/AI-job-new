@php
    $active = $activeNav ?? '';
    $linkClass = function (string $key) use ($active) {
        return $active === $key
            ? 'flex items-center gap-3 px-4 py-3 border-l-4 border-secondary bg-secondary/10 text-secondary font-bold transition-all duration-200'
            : 'flex items-center gap-3 px-4 py-3 text-on-surface-variant opacity-70 hover:bg-surface-container-high transition-colors rounded-lg';
    };
@endphp
<aside class="fixed left-0 top-0 h-screen w-[280px] bg-white/80 backdrop-blur-xl border-r border-outline-variant shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] z-50 flex flex-col py-8 px-4">
    <div class="mb-10 px-4">
        <a href="{{ route('admin.dashboard') }}" class="block hover:opacity-80 transition-opacity">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:var(--brand-gradient);">
                    @if (file_exists(public_path('images/logo.webp')))
                        <img
                            src="{{ asset('images/logo.webp') }}"
                            alt="{{ config('app.name') }} logo"
                            class="w-[22px] h-[22px] brand-logo-img"
                            loading="eager"
                            decoding="async"
                        >
                    @else
                        <span class="material-symbols-outlined text-white text-[18px]">admin_panel_settings</span>
                    @endif
                </div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-primary">{{ config('app.name') }}</h1>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70 pl-11">Admin Portal</p>
        </a>
    </div>
    <nav class="flex-1 space-y-1">
        <a class="{{ $linkClass('dashboard') }}" href="{{ route('admin.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-title-md text-title-md">Dashboard</span>
        </a>
        <a class="{{ $linkClass('applications') }}" href="{{ route('admin.job-applications.index') }}">
            <span class="material-symbols-outlined">assignment</span>
            <span class="font-title-md text-title-md">Job Applications</span>
        </a>
        <a class="{{ $linkClass('analytics') }}" href="{{ route('admin.analytics') }}">
            <span class="material-symbols-outlined">analytics</span>
            <span class="font-title-md text-title-md">Analytics</span>
        </a>
        <a class="{{ $linkClass('profile') }}" href="{{ route('admin.profile') }}">
            <span class="material-symbols-outlined">person</span>
            <span class="font-title-md text-title-md">My Profile</span>
        </a>
    </nav>
    <div class="mt-auto px-4 pt-4 border-t border-outline-variant space-y-3">
        <a href="{{ route('admin.profile') }}" class="font-body-sm text-on-surface-variant truncate hover:text-secondary block">{{ auth()->user()->name }}</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-2 text-sm font-label-caps border border-outline-variant rounded-lg hover:bg-surface-container-high transition-colors">Logout</button>
        </form>
    </div>
</aside>
