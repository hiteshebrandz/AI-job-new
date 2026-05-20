<section class="hidden lg:flex lg:w-[55%] relative overflow-hidden items-center justify-center p-12" style="background: radial-gradient(ellipse 80% 70% at 20% 20%, rgba(124,58,237,0.3) 0%, transparent 55%), radial-gradient(ellipse 60% 60% at 80% 80%, rgba(6,182,212,0.2) 0%, transparent 55%), #0A0F1E;">
    <!-- Animated blobs -->
    <div class="blob blob-violet w-80 h-80 top-0 left-0 animate-blob opacity-40"></div>
    <div class="blob blob-cyan w-64 h-64 bottom-20 right-10 animate-blob opacity-30" style="animation-delay: 2s;"></div>
    <div class="blob blob-blue w-72 h-72 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 animate-blob opacity-20" style="animation-delay: 4s;"></div>

    <!-- Dot grid -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(rgba(139,92,246,0.6) 1px, transparent 1px); background-size: 32px 32px;"></div>

    <!-- Content -->
    <div class="relative z-10 max-w-md">
        <!-- Brand -->
        <a href="{{ route('landing') }}" class="flex items-center gap-3 mb-12">
            <div class="w-10 h-10 rounded-xl gradient-violet flex items-center justify-center shadow-lg ai-glow">
                <span class="material-symbols-outlined text-white text-[22px]" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
            </div>
            <span class="text-xl font-bold tracking-tight gradient-text-violet">Elements HR</span>
        </a>

        <!-- Heading -->
        <h1 class="text-[44px] font-extrabold text-white leading-[1.15] tracking-tight mb-6">
            Redefining the<br>
            <span class="gradient-text-violet">Future of Hiring</span>
        </h1>

        <p class="text-[15px] text-[#94A3B8] leading-relaxed mb-10">
            AI-powered talent matching, resume parsing, and executive search — all in one platform built for the modern workforce.
        </p>

        <!-- Quote card -->
        <div class="glass-card p-6 mb-8">
            <p class="text-[14px] text-[#CBD5E1] italic leading-relaxed mb-4">
                "The most sophisticated way to manage human capital is the seamless synthesis of intelligence and empathy."
            </p>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full gradient-cyan-violet flex items-center justify-center">
                    <span class="text-white font-bold text-[11px]">EH</span>
                </div>
                <div>
                    <p class="text-[12px] font-semibold text-[#E2E8F0]">Elements HR Team</p>
                    <p class="text-[11px] text-[#475569]">Platform Intelligence</p>
                </div>
            </div>
        </div>

        <!-- Stats row -->
        <div class="grid grid-cols-3 gap-4">
            <div class="glass-card p-4 text-center">
                <p class="text-[22px] font-extrabold gradient-text-violet">98%</p>
                <p class="text-[11px] text-[#475569] uppercase tracking-wide mt-1">Match Accuracy</p>
            </div>
            <div class="glass-card p-4 text-center">
                <p class="text-[22px] font-extrabold gradient-text-cyan">24K+</p>
                <p class="text-[11px] text-[#475569] uppercase tracking-wide mt-1">Candidates</p>
            </div>
            <div class="glass-card p-4 text-center">
                <p class="text-[22px] font-extrabold gradient-text-violet">500+</p>
                <p class="text-[11px] text-[#475569] uppercase tracking-wide mt-1">Companies</p>
            </div>
        </div>
    </div>
</section>
