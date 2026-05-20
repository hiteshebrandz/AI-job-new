@extends('layouts.app')

@section('title', 'Executive Recruitment Suite')

@section('body-class', 'bg-background text-on-surface font-body-md overflow-x-hidden')

@section('page-css', 'landing_page.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
@include('partials.nav.public-header')

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden hero-gradient pt-16">
    <!-- Animated background blobs -->
    <div class="blob blob-violet w-[600px] h-[600px] -top-32 -left-32 opacity-25 animate-blob"></div>
    <div class="blob blob-cyan w-[500px] h-[500px] -bottom-32 -right-32 opacity-20 animate-blob" style="animation-delay: 3s;"></div>
    <div class="blob blob-blue w-[400px] h-[400px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-15 animate-blob" style="animation-delay: 6s;"></div>

    <!-- Grid overlay -->
    <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(139,92,246,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(139,92,246,0.5) 1px, transparent 1px); background-size: 60px 60px;"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Left: Copy -->
            <div class="animate-fade-in">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#1E1B4B]/80 border border-[#8B5CF6]/30 mb-8">
                    <span class="ai-pulse-dot"></span>
                    <span class="text-[12px] font-semibold text-[#C4B5FD] uppercase tracking-widest">Next-Gen HR Technology</span>
                </div>

                <h1 class="text-[52px] lg:text-[64px] font-extrabold text-white leading-[1.1] tracking-tight mb-6">
                    Precision Engineering<br>
                    for <span class="gradient-text-violet">Modern Talent</span>
                </h1>

                <p class="text-[17px] text-[#94A3B8] leading-relaxed mb-10 max-w-lg">
                    Elements HR uses advanced AI-driven logic to bridge the gap between world-class talent and global executive opportunities. Parse, match, and hire smarter.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-12">
                    <a href="{{ route('register') }}" class="btn-primary py-4 px-8 text-[15px] font-semibold">
                        <span class="material-symbols-outlined">rocket_launch</span>
                        Get Started Free
                    </a>
                    <a href="{{ route('hr.dashboard') }}" class="btn-ghost py-4 px-8 text-[15px]">
                        <span class="material-symbols-outlined">business_center</span>
                        For Employers
                    </a>
                </div>

                <!-- Social proof -->
                <div class="flex items-center gap-4">
                    <div class="flex -space-x-3">
                        @for ($i = 0; $i < 4; $i++)
                        <div class="w-10 h-10 rounded-full border-2 border-[#0F172A] gradient-violet flex items-center justify-center">
                            <span class="text-white font-bold text-[11px]">{{ ['AK','MR','SL','JB'][$i] }}</span>
                        </div>
                        @endfor
                        <div class="w-10 h-10 rounded-full border-2 border-[#0F172A] bg-[#1E293B] flex items-center justify-center">
                            <span class="text-[#8B5CF6] font-bold text-[11px]">+24k</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex gap-0.5 mb-1">
                            @for ($i = 0; $i < 5; $i++)
                            <span class="material-symbols-outlined text-[#FBBF24] text-[14px]" style="font-variation-settings: 'FILL' 1;">star</span>
                            @endfor
                        </div>
                        <p class="text-[12px] text-[#475569]">Trusted by 24,000+ professionals</p>
                    </div>
                </div>
            </div>

            <!-- Right: AI card -->
            <div class="animate-fade-in-delay-2">
                <div class="glass-card p-8 relative overflow-hidden animate-float-slow">
                    <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-[#8B5CF6]/20 blur-xl"></div>

                    <div class="flex items-center justify-between mb-7">
                        <div>
                            <p class="text-[11px] font-semibold text-[#06B6D4] uppercase tracking-widest mb-1">Live Engine</p>
                            <h3 class="text-[18px] font-bold text-[#E2E8F0]">AI-Matching in Progress</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl gradient-violet flex items-center justify-center animate-pulse-glow">
                            <span class="material-symbols-outlined text-white text-[22px]" style="font-variation-settings: 'FILL' 1;">model_training</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach ([['Principal Product Designer','TechFlow Systems', 98], ['VP of Engineering','Quantix AI', 92], ['Chief Data Officer','Stellar Corp', 87]] as $match)
                        <div class="flex items-center gap-4 bg-[#162032] p-4 rounded-xl border border-[#334155]">
                            <div class="w-11 h-11 rounded-xl gradient-violet flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-white text-[18px]">person</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-semibold text-[#E2E8F0] truncate">{{ $match[0] }}</p>
                                <p class="text-[12px] text-[#64748B]">Matching with '{{ $match[1] }}'</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-[16px] font-extrabold text-[#8B5CF6]">{{ $match[2] }}%</span>
                                <div class="progress-bar-track w-16 mt-1">
                                    <div class="progress-bar-fill" style="width: {{ $match[2] }}%;"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-5 border-t border-[#334155] flex items-center justify-between">
                        <span class="text-[12px] text-[#475569]">Processing 1,247 candidates</span>
                        <span class="badge-ai">Live</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     TRUSTED BY
     ============================================================ -->
<section class="py-10 border-y border-[#1E293B]" style="background: #0A1120;">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <p class="text-center text-[11px] font-semibold text-[#334155] uppercase tracking-widest mb-8">Trusted by Global Leaders</p>
        <div class="flex flex-wrap justify-center items-center gap-10 lg:gap-20 opacity-40">
            @foreach (['VOLVO', 'ORACLE', 'STRIPE', 'AIRBNB', 'ADOBE', 'NOTION'] as $brand)
            <span class="text-[18px] font-extrabold text-[#94A3B8] tracking-tighter hover:text-[#8B5CF6] hover:opacity-100 transition-all cursor-default">{{ $brand }}</span>
            @endforeach
        </div>
    </div>
</section>

<!-- ============================================================
     ANIMATED STATS
     ============================================================ -->
<section class="py-20" style="background: #0F172A;">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['98%', 'Match Accuracy', 'target', '#8B5CF6'],
                ['24K+', 'Candidates', 'people', '#06B6D4'],
                ['500+', 'Companies', 'business', '#34D399'],
                ['12ms', 'Match Latency', 'bolt', '#FBBF24'],
            ] as $stat)
            <div class="glass-card p-7 text-center stat-card">
                <div class="w-12 h-12 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background: {{ $stat[3] }}1a;">
                    <span class="material-symbols-outlined text-[24px]" style="color: {{ $stat[3] }};">{{ $stat[2] }}</span>
                </div>
                <p class="text-[36px] font-extrabold text-[#E2E8F0] mb-1 stat-number" data-value="{{ $stat[0] }}">{{ $stat[0] }}</p>
                <p class="text-[13px] text-[#64748B]">{{ $stat[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ============================================================
     HOW IT WORKS
     ============================================================ -->
<section id="workflow" class="py-24" style="background: #0A1120;">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="badge-ai mb-4">How it Works</span>
            <h2 class="text-[40px] font-extrabold text-[#E2E8F0] tracking-tight mt-3 mb-4">Precision Workflow</h2>
            <p class="text-[16px] text-[#64748B] max-w-2xl mx-auto">Our three-step proprietary engine ensures the highest signal-to-noise ratio in executive search.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ([
                ['01', 'Smart Upload', 'upload_file', 'Drop your resume or job description. Our system accepts all formats and begins immediate analysis.', '#7C3AED'],
                ['02', 'Semantic Parsing', 'analytics', 'We extract more than keywords. Our AI understands skills, cultural fit, and professional trajectory.', '#06B6D4'],
                ['03', 'Neural Matching', 'auto_awesome', 'Receive a curated list of matches with detailed compatibility scoring and predictive performance data.', '#34D399'],
            ] as $step)
            <div class="glass-card glass-card-lift p-8 group">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform" style="background: {{ $step[4] }}1a; border: 1px solid {{ $step[4] }}30;">
                    <span class="material-symbols-outlined text-[28px]" style="color: {{ $step[4] }};">{{ $step[2] }}</span>
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-widest mb-3" style="color: {{ $step[4] }};">{{ $step[0] }}</p>
                <h3 class="text-[20px] font-bold text-[#E2E8F0] mb-3">{{ $step[1] }}</h3>
                <p class="text-[14px] text-[#64748B] leading-relaxed">{{ $step[3] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURES
     ============================================================ -->
<section class="py-24" style="background: #0F172A;">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="badge-violet mb-6 inline-flex">Why Industry Leaders Choose Us</span>
                <h2 class="text-[40px] font-extrabold text-[#E2E8F0] tracking-tight mt-3 mb-6">Engineered for Excellence</h2>
                <p class="text-[16px] text-[#64748B] leading-relaxed mb-10">We don't just find employees — we engineer success. Our platform combines deep-learning algorithms with human-centric design to deliver talent that sticks.</p>

                <ul class="space-y-5">
                    @foreach ([
                        ['Executive Grade Security', 'Enterprise-level data protection and privacy compliance globally.', '#8B5CF6'],
                        ['Diversity-First Logic', 'Algorithmic bias neutralization for a truly equitable hiring process.', '#06B6D4'],
                        ['Predictive Analytics', 'Forecast long-term performance and cultural alignment before the first interview.', '#34D399'],
                        ['Real-Time Matching', 'Live candidate-to-job matching updated as new resumes and listings arrive.', '#FBBF24'],
                    ] as $feature)
                    <li class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5" style="background: {{ $feature[2] }}15; border: 1px solid {{ $feature[2] }}30;">
                            <span class="material-symbols-outlined text-[16px]" style="color: {{ $feature[2] }}; font-variation-settings: 'FILL' 1;">check</span>
                        </div>
                        <div>
                            <h4 class="text-[15px] font-semibold text-[#E2E8F0] mb-1">{{ $feature[0] }}</h4>
                            <p class="text-[13px] text-[#64748B] leading-relaxed">{{ $feature[1] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Stats grid -->
            <div class="grid grid-cols-2 gap-5">
                <div class="glass-card p-7 text-center">
                    <p class="text-[48px] font-extrabold gradient-text-violet mb-2">98%</p>
                    <p class="text-[13px] text-[#64748B]">Match Accuracy</p>
                </div>
                <div class="glass-card p-7 text-center">
                    <p class="text-[48px] font-extrabold gradient-text-cyan mb-2">12ms</p>
                    <p class="text-[13px] text-[#64748B]">Match Latency</p>
                </div>
                <div class="glass-card p-7 text-center">
                    <p class="text-[48px] font-extrabold gradient-text-violet mb-2">94%</p>
                    <p class="text-[13px] text-[#64748B]">Retention Rate</p>
                </div>
                <div class="glass-card p-7 text-center">
                    <p class="text-[48px] font-extrabold gradient-text-cyan mb-2">500+</p>
                    <p class="text-[13px] text-[#64748B]">Companies</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS
     ============================================================ -->
<section class="py-24" style="background: #0A1120;">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="badge-ai mb-4">Testimonials</span>
            <h2 class="text-[40px] font-extrabold text-[#E2E8F0] tracking-tight mt-3">Trusted by Professionals</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ([
                ['"Placing our CTO in under two weeks was unimaginable before Elements HR. The AI matching is genuinely remarkable."', 'Sarah K.', 'Chief People Officer, TechFlow', 'SK'],
                ['"The resume parsing accuracy eliminated 80% of our manual screening work. ROI within the first month."', 'Marcus R.', 'Head of Talent, Quantix AI', 'MR'],
                ['"I went from an unoptimized resume to multiple interviews in 3 days. The AI insights are transformative."', 'Priya M.', 'Senior Software Engineer', 'PM'],
            ] as $t)
            <div class="glass-card glass-card-lift p-7 flex flex-col gap-5">
                <div class="text-[#8B5CF6] text-[32px] font-serif leading-none">"</div>
                <p class="text-[14px] text-[#CBD5E1] leading-relaxed flex-1">{{ $t[0] }}</p>
                <div class="flex items-center gap-3 pt-4 border-t border-[#334155]">
                    <div class="w-9 h-9 rounded-full gradient-violet flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold text-[12px]">{{ $t[3] }}</span>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#E2E8F0]">{{ $t[1] }}</p>
                        <p class="text-[11px] text-[#475569]">{{ $t[2] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ============================================================
     CTA
     ============================================================ -->
<section class="py-24 relative overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1a0d2e 50%, #0F172A 100%);">
    <div class="blob blob-violet w-96 h-96 top-0 left-1/4 opacity-20 animate-blob"></div>
    <div class="blob blob-cyan w-80 h-80 bottom-0 right-1/4 opacity-15 animate-blob" style="animation-delay:4s;"></div>

    <div class="relative z-10 max-w-3xl mx-auto text-center px-6">
        <span class="badge-ai mb-6 inline-flex">Start Today</span>
        <h2 class="text-[48px] font-extrabold text-white tracking-tight mt-3 mb-6">
            Ready to Find Your<br><span class="gradient-text-violet">Perfect Match?</span>
        </h2>
        <p class="text-[17px] text-[#94A3B8] mb-10 leading-relaxed">Join 24,000+ professionals using Elements HR to accelerate their careers and hiring pipelines.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="btn-primary py-4 px-10 text-[16px] font-semibold">
                <span class="material-symbols-outlined">rocket_launch</span>
                Get Started Free
            </a>
            <a href="{{ route('login') }}" class="btn-ghost py-4 px-10 text-[16px]">Sign In</a>
        </div>
    </div>
</section>

@include('partials.nav.public-footer')
@endsection

@push('scripts')
<script>
// Intersection Observer for stat counter animation
(function () {
    var stats = document.querySelectorAll('.stat-number');
    var observed = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var raw = el.getAttribute('data-value') || '';
            var num = parseInt(raw.replace(/[^0-9]/g, ''), 10);
            if (!num) return;
            var suffix = raw.replace(/[0-9]/g, '').replace('+', '').trim();
            var prefix = raw.match(/^[^0-9]*/) ? raw.match(/^[^0-9]*/)[0] : '';
            var start = 0;
            var duration = 1200;
            var step = Math.ceil(num / (duration / 16));
            el.textContent = prefix + '0' + suffix;
            var timer = setInterval(function () {
                start += step;
                if (start >= num) { start = num; clearInterval(timer); }
                el.textContent = prefix + start.toLocaleString() + (raw.includes('+') ? '+' : '') + suffix.replace('+', '');
            }, 16);
            observed.unobserve(el);
        });
    }, { threshold: 0.3 });
    stats.forEach(function (el) { observed.observe(el); });
})();
</script>
@endpush
