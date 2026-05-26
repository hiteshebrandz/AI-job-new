<header class="fixed top-0 right-0 w-full h-16 z-50 glass-panel" style="border-bottom: 1px solid var(--border-subtle);">
    <div class="flex justify-between items-center px-6 lg:px-8 max-w-7xl mx-auto h-full">
        {{-- Brand --}}
        <a href="{{ route('landing') }}" class="flex items-center gap-2.5 hover:opacity-90 transition-opacity">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--brand-gradient);">
                <span class="material-symbols-outlined text-white text-[15px]">auto_awesome</span>
            </div>
            <span class="font-bold text-[18px] tracking-tight gradient-text-violet" style="font-family:'Plus Jakarta Sans',sans-serif;">Elements HR</span>
        </a>

        {{-- Nav (desktop) --}}
        <nav class="hidden md:flex items-center gap-8">
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
        <div class="flex items-center gap-3">
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
                    <button type="submit" class="text-[13px] font-medium transition-colors hover:text-secondary px-2" style="color:var(--text-muted);">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex text-[13px] font-medium transition-colors hover:text-secondary px-3 py-2" style="color:var(--text-muted);">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary py-2 px-5 text-[13px]">Get Started</a>
            @endauth

            {{-- Mobile menu toggle --}}
            <button type="button"
                class="md:hidden p-2 rounded-xl transition-all" style="color:var(--text-muted);"
                onclick="toggleMobileMenu()" aria-label="Menu">
                <span class="material-symbols-outlined text-[22px]">menu</span>
            </button>
        </div>
    </div>

    {{-- Mobile nav drawer --}}
    <div id="mobile-nav" class="hidden md:hidden px-6 py-4 space-y-2" style="background:var(--bg-surface); border-top: 1px solid var(--border-subtle);">
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
