<header id="site-header" class="site-header fixed top-0 right-0 w-full z-50">
    <div id="site-header-inner" class="site-header-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Brand --}}
        <a href="{{ route('landing') }}" class="site-brand group">
            <div class="site-brand-mark">
                @if (file_exists(public_path('images/logo.webp')))
                    <img
                        src="{{ asset('images/logo.webp') }}"
                        alt="{{ config('app.name') }} logo"
                        class="w-[24px] h-[24px] brand-logo-img"
                        loading="eager"
                        decoding="async"
                    >
                @else
                    <span class="material-symbols-outlined text-white text-[18px]">auto_awesome</span>
                @endif
            </div>
            <span class="site-brand-text">{{ config('app.name') }}</span>
        </a>

        {{-- Nav (desktop) --}}
        <nav class="hidden md:flex items-center gap-7 lg:gap-8">
            <a class="text-[13px] font-medium transition-colors hover:text-secondary" style="color:var(--text-muted);"
                href="{{ route('landing') }}#modules">Platform</a>
            <a class="text-[13px] font-medium transition-colors hover:text-secondary" style="color:var(--text-muted);"
                href="{{ route('landing') }}#candidates">For Candidates</a>
            <a class="text-[13px] font-medium transition-colors hover:text-secondary" style="color:var(--text-muted);"
                href="{{ route('landing') }}#employers">For Employers</a>
            <a class="text-[13px] font-medium transition-colors hover:text-secondary" style="color:var(--text-muted);"
                href="{{ route('landing') }}#workflow">How it Works</a>
        </nav>

        {{-- Actions --}}
        <div class="flex items-center gap-2.5 sm:gap-3">
            @auth
                @if (auth()->user()->isUser())
                    <a href="{{ route('user.dashboard') }}" class="btn-primary py-2 px-4 text-[13px]">Dashboard</a>
                @elseif (auth()->user()->isHr())
                    <a href="{{ route('hr.dashboard') }}" class="btn-primary py-2 px-4 text-[13px]">Dashboard</a>
                @elseif (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn-primary py-2 px-4 text-[13px]">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="site-header-link">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="site-header-link hidden sm:inline-flex">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary py-2 px-5 text-[13px]">Get Started</a>
            @endauth

            {{-- Mobile menu toggle --}}
            <button type="button"
                class="site-header-menu-btn md:hidden"
                onclick="toggleMobileMenu()" aria-label="Menu">
                <span class="material-symbols-outlined text-[22px]">menu</span>
            </button>
        </div>
    </div>

    {{-- Mobile nav drawer --}}
    <div id="mobile-nav" class="site-mobile-nav hidden md:hidden px-6 py-4 space-y-2">
        <a class="block py-2.5 text-[14px] font-medium transition-colors hover:text-secondary" style="color:var(--text-secondary);"
            href="{{ route('landing') }}#modules">Platform</a>
        <a class="block py-2.5 text-[14px] font-medium transition-colors hover:text-secondary" style="color:var(--text-secondary);"
            href="{{ route('landing') }}#candidates">For Candidates</a>
        <a class="block py-2.5 text-[14px] font-medium transition-colors hover:text-secondary" style="color:var(--text-secondary);"
            href="{{ route('landing') }}#employers">For Employers</a>
        <a class="block py-2.5 text-[14px] font-medium transition-colors hover:text-secondary" style="color:var(--text-secondary);"
            href="{{ route('landing') }}#workflow">How it Works</a>
        @guest
            <div class="pt-2 flex gap-3" style="border-top: 1px solid var(--border-subtle);">
                <a href="{{ route('login') }}" class="btn-ghost py-2 px-4 text-[13px] flex-1 justify-center">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary py-2 px-4 text-[13px] flex-1 justify-center">Register</a>
            </div>
        @endguest
    </div>
</header>
<script>
    function toggleMobileMenu() {
        var nav = document.getElementById('mobile-nav');
        if (nav) nav.classList.toggle('hidden');
    }
</script>
