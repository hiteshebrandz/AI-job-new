<footer class="relative overflow-hidden" style="background: var(--bg-surface); border-top: 1px solid var(--border-subtle);">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-14 pb-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
            {{-- Brand --}}
            <div>
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:var(--brand-gradient);">
                        <span class="material-symbols-outlined text-white text-[18px]">auto_awesome</span>
                    </div>
                    <span class="font-bold text-xl tracking-tight gradient-text-violet" style="font-family:'Plus Jakarta Sans',sans-serif;">Elements HR</span>
                </a>
                <p class="text-[14px] leading-relaxed max-w-xs" style="color:var(--text-muted);">The executive suite for modern human resource management and AI-powered talent matching.</p>
            </div>

            {{-- Links --}}
            <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-8">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest mb-4" style="color:var(--text-muted);">Platform</p>
                    <ul class="space-y-3">
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('landing') }}#modules">Modules</a></li>
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('landing') }}#ai-tools">AI Tools</a></li>
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('landing') }}#workflow">How it Works</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest mb-4" style="color:var(--text-muted);">Solutions</p>
                    <ul class="space-y-3">
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('landing') }}#candidates">For Candidates</a></li>
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('landing') }}#employers">For Employers</a></li>
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('landing') }}#jobs">Job Board</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest mb-4" style="color:var(--text-muted);">Company</p>
                    <ul class="space-y-3">
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('landing') }}">About</a></li>
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('login') }}">Careers</a></li>
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('sitemap') }}">Site Map</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest mb-4" style="color:var(--text-muted);">Account</p>
                    <ul class="space-y-3">
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('register') }}">Get Started</a></li>
                        <li><a class="text-[14px] transition-colors hover:text-secondary" style="color:var(--text-secondary);" href="{{ route('login') }}">Sign In</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="divider mb-8"></div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[13px]" style="color:var(--text-muted);">© {{ date('Y') }} Elements HR Services. All rights reserved.</p>
            <div class="flex gap-6 flex-wrap">
                <a class="text-[12px] transition-colors hover:text-secondary" style="color:var(--text-muted);" href="{{ route('landing') }}">Privacy Policy</a>
                <a class="text-[12px] transition-colors hover:text-secondary" style="color:var(--text-muted);" href="{{ route('landing') }}">Terms of Service</a>
                <a class="text-[12px] transition-colors hover:text-secondary" style="color:var(--text-muted);" href="{{ route('login') }}">Contact Support</a>
            </div>
        </div>
    </div>
</footer>
