@php
    $authUser = auth()->user();
    $isCandidate = $authUser->isUser();
    $unreadNotifications = $isCandidate
        ? $authUser->applicationNotifications()->where('is_read', false)->count()
        : 0;
    $candidate = $authUser->candidate;
    $initials = $candidate?->initials() ?? strtoupper(substr($authUser->name, 0, 2));
@endphp
<div class="flex items-center gap-4 relative">
@if ($isCandidate)
<button type="button" id="notifications-btn" class="relative text-on-surface-variant hover:text-secondary transition-colors">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
@if ($unreadNotifications > 0)
<span id="notifications-badge" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-secondary text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</span>
@endif
</button>
<div id="notifications-panel" class="hidden absolute right-0 top-12 w-80 max-h-96 overflow-y-auto bg-white border border-outline-variant rounded-xl shadow-xl z-50 p-4"></div>
@endif
<div class="relative" id="profile-dropdown-wrap">
<button type="button" id="profile-dropdown-btn" class="w-10 h-10 rounded-full bg-secondary/10 border border-outline-variant flex items-center justify-center hover:ring-2 hover:ring-secondary/30 transition-all" aria-expanded="false">
<span class="font-label-caps text-secondary font-bold text-sm">{{ $initials }}</span>
</button>
<div id="profile-dropdown-menu" class="hidden absolute right-0 top-12 w-56 bg-white border border-outline-variant rounded-xl shadow-xl z-50 py-2">
<div class="px-4 py-3 border-b border-outline-variant">
<p class="font-title-md text-title-md text-primary truncate">{{ $authUser->name }}</p>
<p class="font-body-sm text-on-surface-variant truncate">{{ $authUser->email }}</p>
</div>
@if ($isCandidate)
<a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">person</span> My Profile
</a>
<a href="{{ route('user.saved-jobs') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">bookmark</span> Saved Jobs
</a>
<a href="{{ route('user.applied-jobs') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">assignment</span> Applied Jobs
</a>
<a href="{{ route('user.settings.notifications') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">settings</span> Account Settings
</a>
@elseif ($authUser->isHr())
<a href="{{ route('hr.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">dashboard</span> Dashboard
</a>
<a href="{{ route('hr.jobs.create') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">post_add</span> Post a Job
</a>
<a href="{{ route('hr.settings.notifications') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">settings</span> Account Settings
</a>
@elseif ($authUser->isAdmin())
<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">dashboard</span> Admin Dashboard
</a>
<a href="{{ route('admin.job-applications.index') }}" class="flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary transition-colors">
<span class="material-symbols-outlined text-[20px]">assignment</span> Job Applications
</a>
@endif
<form method="POST" action="{{ route('logout') }}" class="border-t border-outline-variant mt-2 pt-2">
@csrf
<button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 font-body-sm text-on-surface-variant hover:bg-surface-container-low hover:text-error transition-colors text-left">
<span class="material-symbols-outlined text-[20px]">logout</span> Logout
</button>
</form>
</div>
</div>
</div>
