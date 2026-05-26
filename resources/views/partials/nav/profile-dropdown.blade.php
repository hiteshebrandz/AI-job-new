@php
    $authUser = auth()->user();
    $isCandidate = $authUser->isUser();
    $unreadNotifications = $isCandidate
        ? $authUser->applicationNotifications()->where('is_read', false)->count()
        : 0;
    $candidate = $authUser->candidate;
    $initials = $authUser->initials();
    $photoUrl = $authUser->profilePhotoUrl();
    $profileUrl = \App\Support\AuthRedirect::profileRouteFor($authUser);
@endphp
<div class="flex items-center gap-2 relative">
    @if ($isCandidate)
        <!-- Notifications bell -->
        <button type="button" id="notifications-btn"
            class="relative p-2 rounded-xl text-on-surface-variant hover:text-secondary hover:bg-surface-container transition-all">
            <span class="material-symbols-outlined text-[22px]" data-icon="notifications">notifications</span>
            @if ($unreadNotifications > 0)
                <span id="notifications-badge"
                    class="absolute top-1.5 right-1.5 min-w-[16px] h-[16px] px-0.5 bg-secondary text-white text-[9px] font-bold rounded-full flex items-center justify-center animate-pulse-glow">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
            @endif
        </button>
        <div id="notifications-panel"
            class="hidden absolute right-0 top-14 w-80 max-h-96 overflow-y-auto glass-card rounded-2xl shadow-xl z-50 p-2">
        </div>
    @endif

    <!-- Avatar / Profile dropdown trigger -->
    <div class="relative" id="profile-dropdown-wrap">
        <button type="button" id="profile-dropdown-btn"
            class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center hover:shadow-glow-violet transition-all text-white font-bold text-[13px] ring-2 ring-transparent hover:ring-secondary/40 {{ $photoUrl ? '' : 'gradient-violet' }}"
            aria-expanded="false">
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="" class="w-full h-full object-cover">
            @else
                {{ $initials }}
            @endif
        </button>

        <!-- Dropdown -->
        <div id="profile-dropdown-menu"
            class="hidden absolute right-0 top-12 w-60 glass-card rounded-2xl shadow-xl z-50 py-2 border border-outline-variant">
            <div class="px-4 py-3 border-b border-outline-variant">
                <p class="text-[14px] font-semibold text-on-surface truncate">{{ $authUser->name }}</p>
                <p class="text-[12px] text-on-surface-variant truncate">{{ $authUser->email }}</p>
            </div>

            <a href="{{ $profileUrl }}"
                class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                <span class="material-symbols-outlined text-[18px]">person</span> My Profile
            </a>
            @if ($isCandidate)
                <a href="{{ route('user.saved-jobs') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">bookmark</span> Saved Jobs
                </a>
                <a href="{{ route('user.applied-jobs') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">assignment</span> Applied Jobs
                </a>
                <a href="{{ route('user.settings.notifications') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">notifications</span> Notifications
                </a>
            @elseif ($authUser->isHr())
                <a href="{{ route('hr.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span> Dashboard
                </a>
                <a href="{{ route('hr.jobs.create') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">post_add</span> Post a Job
                </a>
                <a href="{{ route('hr.settings.notifications') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">notifications</span> Notifications
                </a>
            @elseif ($authUser->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span> Admin Dashboard
                </a>
                <a href="{{ route('admin.job-applications.index') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-surface-container-high hover:text-secondary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">assignment</span> Applications
                </a>
            @endif

            <div class="border-t border-outline-variant mt-1 pt-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-[13px] text-on-surface-variant hover:bg-[var(--badge-error-bg)] hover:text-[var(--badge-error-text)] transition-colors text-left">
                        <span class="material-symbols-outlined text-[18px]">logout</span> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>