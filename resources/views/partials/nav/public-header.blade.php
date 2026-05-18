<header class="fixed top-0 right-0 w-full h-16 z-50 bg-surface/80 backdrop-blur-lg border-b border-outline-variant">
    <div class="flex justify-between items-center px-gutter max-w-[1440px] mx-auto h-full">
        <a href="{{ route('landing') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <span class="font-headline-lg text-headline-lg font-bold text-primary">Elements HR</span>
        </a>
        <nav class="hidden md:flex items-center gap-8">
            <a class="text-on-surface-variant font-label-caps text-label-caps hover:text-secondary transition-colors" href="{{ route('suite.one') }}">Solutions</a>
            <a class="text-on-surface-variant font-label-caps text-label-caps hover:text-secondary transition-colors" href="{{ route('landing') }}#workflow">How it Works</a>
            <a class="text-on-surface-variant font-label-caps text-label-caps hover:text-secondary transition-colors" href="{{ route('login') }}">Analytics</a>
            <a class="text-on-surface-variant font-label-caps text-label-caps hover:text-secondary transition-colors" href="{{ route('suite.two') }}">Pricing</a>
        </nav>
        <div class="flex items-center gap-4">
            @auth
                @if (auth()->user()->isUser())
                    <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-secondary text-white rounded-lg font-label-caps text-label-caps hover:bg-secondary-container transition-colors">Dashboard</a>
                @elseif (auth()->user()->isHr())
                    <a href="{{ route('hr.dashboard') }}" class="px-4 py-2 bg-secondary text-white rounded-lg font-label-caps text-label-caps hover:bg-secondary-container transition-colors">Dashboard</a>
                @elseif (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-secondary text-white rounded-lg font-label-caps text-label-caps hover:bg-secondary-container transition-colors">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="font-label-caps text-label-caps text-on-surface-variant hover:text-secondary">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex px-4 py-2 text-secondary font-label-caps text-label-caps hover:underline">Sign In</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-secondary text-white rounded-lg font-label-caps text-label-caps hover:bg-secondary-container transition-colors">Register</a>
            @endauth
        </div>
    </div>
</header>
