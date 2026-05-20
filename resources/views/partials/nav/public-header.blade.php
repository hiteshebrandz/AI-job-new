<header class="fixed top-0 right-0 w-full h-16 z-50 glass-panel border-b border-[#1E293B]">
    <div class="flex justify-between items-center px-6 lg:px-8 max-w-7xl mx-auto h-full">
        <!-- Brand -->
        <a href="{{ route('landing') }}" class="flex items-center gap-2.5 hover:opacity-90 transition-opacity">
            <div class="w-7 h-7 rounded-lg gradient-violet flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-[15px]">auto_awesome</span>
            </div>
            <span class="font-bold text-[18px] tracking-tight gradient-text-violet">Elements HR</span>
        </a>

        <!-- Nav (desktop) -->
        <nav class="hidden md:flex items-center gap-8">
            <a class="text-[13px] font-medium text-[#64748B] hover:text-[#C4B5FD] transition-colors"
                href="{{ route('suite.one') }}">Solutions</a>
            <a class="text-[13px] font-medium text-[#64748B] hover:text-[#C4B5FD] transition-colors"
                href="{{ route('landing') }}#workflow">How it Works</a>
            <a class="text-[13px] font-medium text-[#64748B] hover:text-[#C4B5FD] transition-colors"
                href="{{ route('suite.two') }}">Pricing</a>
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <button onclick="toggleTheme()"
                class="p-2 rounded-xl text-[#64748B] hover:text-[#C4B5FD] hover:bg-[#1E293B] transition-all hidden sm:flex">
                <span class="material-symbols-outlined text-[20px]" data-theme-icon>light_mode</span>
            </button>

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
                    <button type="submit"
                        class="text-[13px] font-medium text-[#64748B] hover:text-[#C4B5FD] transition-colors px-2">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="hidden sm:inline-flex text-[13px] font-medium text-[#94A3B8] hover:text-[#C4B5FD] transition-colors px-3 py-2">Sign
                    In</a>
                <a href="{{ route('register') }}" class="btn-primary py-2 px-5 text-[13px]">Get Started</a>
            @endauth

            <!-- Mobile menu toggle -->
            <button type="button"
                class="md:hidden p-2 rounded-xl text-[#64748B] hover:text-[#E2E8F0] hover:bg-[#1E293B] transition-all"
                onclick="toggleMobileMenu()" aria-label="Menu">
                <span class="material-symbols-outlined text-[22px]">menu</span>
            </button>
        </div>
    </div>

    <!-- Mobile nav drawer -->
    <div id="mobile-nav" class="hidden md:hidden glass-panel border-t border-[#1E293B] px-6 py-4 space-y-2">
        <a class="block py-2.5 text-[14px] font-medium text-[#94A3B8] hover:text-[#C4B5FD] transition-colors"
            href="{{ route('suite.one') }}">Solutions</a>
        <a class="block py-2.5 text-[14px] font-medium text-[#94A3B8] hover:text-[#C4B5FD] transition-colors"
            href="{{ route('landing') }}#workflow">How it Works</a>
        <a class="block py-2.5 text-[14px] font-medium text-[#94A3B8] hover:text-[#C4B5FD] transition-colors"
            href="{{ route('suite.two') }}">Pricing</a>
        @guest
            <div class="pt-2 border-t border-[#334155] flex gap-3">
                <a href="{{ route('login') }}" class="btn-ghost py-2 px-4 text-[13px] flex-1 justify-center">Sign In</a>
                <a href="{{ route('register') }}"
                    class="btn-primary py-2 px-4 text-[13px] flex-1 justify-center">Register</a>
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