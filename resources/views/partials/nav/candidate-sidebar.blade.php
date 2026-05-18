@php
    $active = $activeNav ?? '';
    $linkClass = function (string $key) use ($active) {
        return $active === $key
            ? 'flex items-center gap-3 px-4 py-3 border-l-4 border-secondary bg-secondary/10 text-secondary font-bold transition-all duration-200'
            : 'flex items-center gap-3 px-4 py-3 text-on-surface-variant dark:text-surface-variant opacity-70 hover:bg-surface-container-high transition-colors rounded-lg';
    };
@endphp
<aside class="fixed left-0 top-0 h-screen w-[280px] bg-white/80 dark:bg-black/80 backdrop-blur-xl border-r border-outline-variant dark:border-outline shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.05)] z-50 flex flex-col py-8 px-4">
    <div class="mb-10 px-4">
        <a href="{{ route('user.dashboard') }}" class="block hover:opacity-80 transition-opacity">
            <h1 class="font-headline-lg text-headline-lg font-bold text-primary dark:text-on-primary-fixed">Elements HR</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">Candidate Portal</p>
        </a>
    </div>
    <nav class="flex-1 space-y-1">
        <a class="{{ $linkClass('dashboard') }}" href="{{ route('user.dashboard') }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-title-md text-title-md">Dashboard</span>
        </a>
        <a class="{{ $linkClass('jobs') }}" href="{{ route('user.jobs.recommendations') }}">
            <span class="material-symbols-outlined">work</span>
            <span class="font-title-md text-title-md">Jobs</span>
        </a>
        <a class="{{ $linkClass('resume') }}" href="{{ route('user.resume.upload') }}">
            <span class="material-symbols-outlined">upload_file</span>
            <span class="font-title-md text-title-md">Resume</span>
        </a>
        <a class="{{ $linkClass('analytics') }}" href="{{ route('user.resume.analytics') }}">
            <span class="material-symbols-outlined">analytics</span>
            <span class="font-title-md text-title-md">Analytics</span>
        </a>
        <a class="{{ $linkClass('settings') }}" href="{{ route('user.settings.notifications') }}">
            <span class="material-symbols-outlined">settings</span>
            <span class="font-title-md text-title-md">Settings</span>
        </a>
    </nav>
    <div class="mt-auto px-4 pt-4 border-t border-outline-variant space-y-3">
        <p class="font-body-sm text-on-surface-variant truncate">{{ auth()->user()->name }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-2 text-sm font-label-caps border border-outline-variant rounded-lg hover:bg-surface-container-high transition-colors">
                Logout
            </button>
        </form>
    </div>
</aside>
