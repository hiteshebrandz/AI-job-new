@php
    $active = $activeNav ?? '';
    $linkClass = function (string $key) use ($active) {
        return $active === $key
            ? 'nav-link-active'
            : 'nav-link';
    };
@endphp
<aside id="app-sidebar" class="sidebar-mobile-hidden fixed left-0 top-0 h-screen w-[280px] glass-panel border-r border-[#1E293B] z-50 flex flex-col py-8 px-4 lg:translate-x-0 transition-transform duration-300">
    <!-- Brand -->
    <div class="mb-8 px-4">
        <a href="{{ route('user.dashboard') }}" class="block group">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-8 h-8 rounded-lg gradient-violet flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-[18px]">auto_awesome</span>
                </div>
                <span class="font-bold text-xl tracking-tight gradient-text-violet">Elements HR</span>
                <span class="ai-pulse-dot ml-auto"></span>
            </div>
            <p class="font-body-sm text-[12px] text-[#475569] pl-11">Candidate Portal</p>
        </a>
    </div>

    <!-- Divider -->
    <div class="divider mx-4 mb-6"></div>

    <!-- Nav Links -->
    <nav class="flex-1 space-y-1 px-2">
        <a class="{{ $linkClass('dashboard') }}" href="{{ route('user.dashboard') }}">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span>Dashboard</span>
        </a>
        <a class="{{ $linkClass('jobs') }}" href="{{ route('user.jobs.recommendations') }}">
            <span class="material-symbols-outlined text-[20px]">work</span>
            <span>Jobs</span>
            <span class="ml-auto badge-ai text-[10px] px-2 py-0.5">AI</span>
        </a>
        <a class="{{ $linkClass('resume') }}" href="{{ route('user.resume.upload') }}">
            <span class="material-symbols-outlined text-[20px]">upload_file</span>
            <span>Resume</span>
        </a>
        <a class="{{ $linkClass('analytics') }}" href="{{ route('user.resume.analytics') }}">
            <span class="material-symbols-outlined text-[20px]">analytics</span>
            <span>Analytics</span>
        </a>
        <a class="{{ $linkClass('saved') }}" href="{{ route('user.saved-jobs') }}">
            <span class="material-symbols-outlined text-[20px]">bookmark</span>
            <span>Saved Jobs</span>
        </a>
        <a class="{{ $linkClass('applied') }}" href="{{ route('user.applied-jobs') }}">
            <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
            <span>Applications</span>
        </a>
        <a class="{{ $linkClass('settings') }}" href="{{ route('user.settings.notifications') }}">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            <span>Settings</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="mt-auto px-2 pt-4">
        <div class="divider mb-4"></div>
        <div class="glass-card p-3 mb-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full gradient-violet flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-[13px]">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[13px] font-semibold text-[#E2E8F0] truncate">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-[#475569] truncate">Candidate</p>
            </div>
            <button onclick="toggleTheme()" class="p-1.5 rounded-lg hover:bg-[#263248] text-[#64748B] transition-colors" title="Toggle theme">
                <span class="material-symbols-outlined text-[16px]" data-theme-icon>light_mode</span>
            </button>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-[13px] font-medium text-[#64748B] hover:text-[#F87171] hover:bg-[#1a1020] rounded-xl transition-all">
                <span class="material-symbols-outlined text-[16px]">logout</span>
                Sign Out
            </button>
        </form>
    </div>
</aside>
