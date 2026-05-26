@php
    $defaultHeaderLeft = view('partials.nav.candidate-header-search-default')->render();
@endphp
<header class="fixed top-0 right-0 w-full lg:w-[calc(100%-280px)] h-[64px] glass-panel border-b z-40" style="border-color: var(--border-default);">
    <div class="flex justify-between items-center h-full px-4 lg:px-8 max-w-[1440px] mx-auto gap-4">
        <!-- Mobile hamburger -->
        <button type="button" class="theme-toggle-btn lg:hidden" onclick="openSidebar()" aria-label="Open menu">
            <span class="material-symbols-outlined">menu</span>
        </button>

        <!-- Header left slot (search bar by default) -->
        <div class="flex items-center flex-1 min-w-0">
            {!! $__env->yieldPushContent('candidate-header-left', $defaultHeaderLeft) !!}
        </div>

        <!-- Right actions -->
        <div class="flex items-center gap-2 lg:gap-4 flex-shrink-0">
            @stack('candidate-header-actions')
            @include('partials.nav.profile-dropdown')
        </div>
    </div>
</header>
