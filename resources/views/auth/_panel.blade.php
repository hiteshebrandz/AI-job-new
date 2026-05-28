<section class="hidden lg:flex lg:w-[55%] relative overflow-hidden items-center justify-center p-12" style="background: var(--brand-gradient);">
    <!-- Soft radial overlays for depth -->
    <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse 80% 70% at 20% 20%, rgba(255,255,255,0.12) 0%, transparent 55%), radial-gradient(ellipse 60% 60% at 80% 80%, rgba(255,255,255,0.08) 0%, transparent 55%);"></div>

    <!-- Dot grid -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(rgba(255,255,255,0.8) 1px, transparent 1px); background-size: 32px 32px;"></div>

    <!-- Floating blobs -->
    <div class="blob w-80 h-80 top-0 left-0 animate-blob" style="background:rgba(255,255,255,0.10); opacity:0.3;"></div>
    <div class="blob w-64 h-64 bottom-20 right-10 animate-blob" style="background:rgba(255,255,255,0.08); opacity:0.2; animation-delay:2s;"></div>

    <!-- Content -->
    <div class="relative z-10 max-w-md">
        <!-- Brand -->
        <a href="{{ route('landing') }}" class="flex items-center gap-3 mb-12">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background:rgba(255,255,255,0.20); border:1px solid rgba(255,255,255,0.35);">
                @if (file_exists(public_path('images/logo.webp')))
                    <img
                        src="{{ asset('images/logo.webp') }}"
                        alt="{{ config('app.name') }} logo"
                        class="w-[26px] h-[26px] brand-logo-img"
                        loading="eager"
                        decoding="async"
                    >
                @else
                    <span class="material-symbols-outlined text-white text-[22px]" style="font-variation-settings:'FILL' 1;">auto_awesome</span>
                @endif
            </div>
            <span class="text-xl font-bold tracking-tight text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ config('app.name') }}</span>
        </a>

        <!-- Heading -->
        <h1 class="text-[44px] font-extrabold text-white leading-[1.15] tracking-tight mb-6" style="font-family:'Plus Jakarta Sans',sans-serif;">
            Redefining the<br>
            <span style="color:rgba(255,255,255,0.75);">Future of Hiring</span>
        </h1>

        <p class="text-[15px] leading-relaxed mb-10" style="color:rgba(255,255,255,0.78);">
            AI-powered talent matching, resume parsing, and executive search — all in one platform built for the modern workforce.
        </p>

        <!-- Quote card -->
        <div class="p-6 mb-8 rounded-2xl" style="background:rgba(255,255,255,0.14); border:1px solid rgba(255,255,255,0.25); backdrop-filter:blur(12px);">
            <p class="text-[14px] italic leading-relaxed mb-4" style="color:rgba(255,255,255,0.82);">
                "The most sophisticated way to manage human capital is the seamless synthesis of intelligence and empathy."
            </p>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:rgba(255,255,255,0.20);">
                    <span class="text-white font-bold text-[11px]">TS</span>
                </div>
                <div>
                    <p class="text-[12px] font-semibold text-white">TalentSync AI Team</p>
                    <p class="text-[11px]" style="color:rgba(255,255,255,0.65);">Platform Intelligence</p>
                </div>
            </div>
        </div>

        <!-- Stats row -->
        <div class="grid grid-cols-3 gap-4">
            @foreach ([['98%','Match Accuracy'],['24K+','Candidates'],['500+','Companies']] as $s)
            <div class="p-4 text-center rounded-2xl" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.22); backdrop-filter:blur(8px);">
                <p class="text-[22px] font-extrabold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $s[0] }}</p>
                <p class="text-[11px] uppercase tracking-wide mt-1" style="color:rgba(255,255,255,0.65);">{{ $s[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
