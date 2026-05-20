<footer class="relative overflow-hidden" style="background: linear-gradient(180deg, #0A1120 0%, #0F172A 100%);">
    <!-- Gradient border top -->
    <div class="h-px w-full" style="background: linear-gradient(90deg, transparent, rgba(139,92,246,0.4), rgba(6,182,212,0.4), transparent);"></div>

    <!-- Background blobs -->
    <div class="blob blob-violet w-96 h-96 -top-20 -left-20 opacity-10 animate-blob"></div>
    <div class="blob blob-cyan w-80 h-80 bottom-0 right-0 opacity-10 animate-blob" style="animation-delay: 3s;"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 pt-16 pb-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
            <!-- Brand -->
            <div>
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-lg gradient-violet flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-[18px]">auto_awesome</span>
                    </div>
                    <span class="font-bold text-xl tracking-tight gradient-text-violet">Elements HR</span>
                </a>
                <p class="text-[14px] text-[#475569] leading-relaxed max-w-xs">The executive suite for modern human resource management and AI-powered talent matching.</p>
            </div>

            <!-- Links -->
            <div class="md:col-span-2 grid grid-cols-2 gap-8">
                <div>
                    <p class="text-[11px] font-semibold text-[#475569] uppercase tracking-widest mb-4">Platform</p>
                    <ul class="space-y-3">
                        <li><a class="text-[14px] text-[#64748B] hover:text-[#C4B5FD] transition-colors" href="{{ route('suite.one') }}">Solutions</a></li>
                        <li><a class="text-[14px] text-[#64748B] hover:text-[#C4B5FD] transition-colors" href="{{ route('landing') }}#workflow">How it Works</a></li>
                        <li><a class="text-[14px] text-[#64748B] hover:text-[#C4B5FD] transition-colors" href="{{ route('suite.two') }}">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-[#475569] uppercase tracking-widest mb-4">Company</p>
                    <ul class="space-y-3">
                        <li><a class="text-[14px] text-[#64748B] hover:text-[#C4B5FD] transition-colors" href="{{ route('landing') }}">About</a></li>
                        <li><a class="text-[14px] text-[#64748B] hover:text-[#C4B5FD] transition-colors" href="{{ route('login') }}">Careers</a></li>
                        <li><a class="text-[14px] text-[#64748B] hover:text-[#C4B5FD] transition-colors" href="{{ route('sitemap') }}">Site Map</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom bar -->
        <div class="divider mb-8"></div>
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[13px] text-[#334155]">© {{ date('Y') }} Elements HR Services. All rights reserved.</p>
            <div class="flex gap-6 flex-wrap">
                <a class="text-[12px] text-[#475569] hover:text-[#8B5CF6] transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
                <a class="text-[12px] text-[#475569] hover:text-[#8B5CF6] transition-colors" href="{{ route('landing') }}">Terms of Service</a>
                <a class="text-[12px] text-[#475569] hover:text-[#8B5CF6] transition-colors" href="{{ route('login') }}">Contact Support</a>
            </div>
        </div>
    </div>
</footer>
