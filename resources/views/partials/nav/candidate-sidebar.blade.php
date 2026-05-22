@php
    $active = $activeNav ?? '';
    $linkClass = function (string $key) use ($active) {
        return $active === $key
            ? 'nav-link-active'
            : 'nav-link';
    };
@endphp
<aside id="app-sidebar"
    class="sidebar-mobile-hidden fixed left-0 top-0 h-screen w-[280px] glass-panel border-r z-50 flex flex-col py-8 px-4 lg:translate-x-0 transition-transform duration-300" style="border-color: var(--border-default);">
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
            <p class="font-body-sm text-[12px] pl-11" style="color: var(--text-muted);">Candidate Portal</p>
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
        <div class="space-y-1">
            <p class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-widest" style="color: var(--text-muted);">Resume</p>
            <a class="{{ $linkClass('resume') }} pl-6" href="{{ route('user.resume.upload') }}">
                <span class="material-symbols-outlined text-[20px]">upload_file</span>
                <span>Upload</span>
            </a>
            <a class="{{ $linkClass('resume-optimizer') }} pl-6" href="{{ route('user.resume.ai-optimizer') }}">
                <span class="material-symbols-outlined text-[20px]">auto_fix_high</span>
                <span>AI Resume Optimizer</span>
                <span class="ml-auto badge-ai text-[10px] px-2 py-0.5">AI</span>
            </a>
        </div>
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
        <a class="{{ $linkClass('profile') }}" href="{{ route('user.profile') }}">
            <span class="material-symbols-outlined text-[20px]">person</span>
            <span>My Profile</span>
        </a>
        <a class="{{ $linkClass('settings') }}" href="{{ route('user.settings.notifications') }}">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            <span>Notifications</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="mt-auto px-2 pt-4">
        <div class="divider mb-4"></div>
        <a href="{{ route('user.profile') }}" class="glass-card p-3 mb-3 flex items-center gap-3 hover:opacity-90 transition-opacity block">
            @php $u = auth()->user(); $photo = $u->profilePhotoUrl(); @endphp
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden {{ $photo ? '' : 'gradient-violet' }}">
                @if ($photo)
                    <img src="{{ $photo }}" alt="" class="w-full h-full object-cover">
                @else
                    <span class="text-white font-bold text-[13px]">{{ $u->initials() }}</span>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[13px] font-semibold truncate" style="color: var(--text-primary);">{{ auth()->user()->name }}</p>
                <p class="text-[11px] truncate" style="color: var(--text-muted);">Candidate</p>
            </div>
            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); toggleTheme();" class="theme-toggle-btn p-1.5 text-[16px]" title="Toggle theme">
                <span class="material-symbols-outlined text-[16px]" data-theme-icon>dark_mode</span>
            </button>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-2 px-3 py-2 text-[13px] font-medium rounded-xl transition-all hover:text-red-500" style="color: var(--text-muted);">
                <span class="material-symbols-outlined text-[16px]">logout</span>
                Sign Out
            </button>
        </form>
    </div>
</aside>