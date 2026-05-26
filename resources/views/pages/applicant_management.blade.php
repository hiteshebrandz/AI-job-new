@extends('layouts.employer', ['activeNav' => 'candidates'])

@section('title', 'Applicant Management')

@section('body-class', 'bg-background text-on-surface font-body-md overflow-hidden')

@section('page-css', 'applicant_management.css')

@section('tailwind-config', 'tailwind-config-admin.js')

@push('candidate-header-actions')
<button type="button" class="btn-primary py-2 px-4 text-[13px]">
    <span class="material-symbols-outlined text-[16px]" data-icon="add">add</span>
    Add Candidate
</button>
@endpush

@section('employer-main-full')
<div class="h-[calc(100vh-4rem)] flex flex-col pt-16">
<section class="flex-1 overflow-hidden flex flex-col" style="background: var(--bg-page);">
<!-- Board Header -->
<div class="px-6 py-5 flex flex-wrap items-center justify-between gap-4 border-b border-outline-variant">
<div>
    <div class="flex items-center gap-2 mb-1">
        <span class="badge-violet text-[10px]">Kanban Board</span>
    </div>
    <h2 class="text-[20px] font-bold text-on-surface">Senior Product Designer Pipeline</h2>
    <p class="text-[13px] text-on-surface-variant">24 total applicants across 5 stages</p>
</div>
<div class="flex items-center gap-2 glass-card p-1 rounded-xl">
    <button class="px-4 py-1.5 rounded-lg bg-surface-container-high text-secondary text-[12px] font-semibold">Board</button>
    <button class="px-4 py-1.5 rounded-lg text-on-surface-variant text-[12px] font-semibold hover:bg-surface-container transition-all">List</button>
</div>
</div>
<!-- Scrollable Kanban Container -->
<div class="flex-1 overflow-x-auto px-gutter pb-8 custom-scrollbar">
<div class="flex gap-6 h-full items-start">
<!-- Column: Applied -->
<div class="kanban-column flex flex-col h-full kanban-column flex-shrink-0">
<div class="p-4 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="font-semibold text-[13px] text-on-surface">Applied</span>
<span class="bg-surface-container-high text-on-surface-variant px-2 py-0.5 rounded-full text-[10px] font-bold">8</span>
</div>
<button class="material-symbols-outlined text-on-surface-variant hover:text-on-surface transition-colors" data-icon="more_horiz">more_horiz</button>
</div>
<div class="flex-1 overflow-y-auto px-3 pb-4 space-y-3 custom-scrollbar">
<!-- Card 1 -->
<div class="kanban-card p-3 cursor-grab active:cursor-grabbing border-l-4 border-l-secondary/40">
<div class="flex justify-between items-start mb-3">
<div class="flex items-center gap-3">
<img alt="Candidate" class="w-10 h-10 rounded-full object-cover" data-alt="A portrait of a young creative professional woman with a warm, engaging expression. She has curly hair and is wearing a stylish terracotta-colored blazer. The background is a clean, minimalist design studio with soft daylight and subtle architectural lines, maintaining a modern, corporate-chic light-mode aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAS9eSRETy3UQpn3nFaaoGekbmNubAgJT4l98udc9SS43003n7ObkZay0lNU450g_F8Zna6cIafu2gBifdQntBnR3kvxAMiJ6gC9p2oGZStY9PDsFOWBHh3pJfAk5_JskCCRY63EyhF83u1mhLNhu1PSRb0n0tF0XLdygn81BQhT4uaomaXiOVNVErOYrdbHb8-mND-VLzIyVUCMPlr8puq42_BAm_XCo8j0lWVpw-McUO5u0qkb5lXF88Mu1AVZKh1-7EjW0BWsGfO"/>
<div>
<p class="font-title-md text-body-sm font-bold">Sarah Jenkins</p>
<p class="text-on-surface-variant text-[12px]">Lead Designer @@ Figma</p>
</div>
</div>
<div class="flex flex-col items-end">
<div class="flex items-center text-secondary font-bold text-[12px]">
<span>98%</span>
<span class="material-symbols-outlined text-[14px] ml-0.5" data-icon="bolt" style="font-variation-settings: 'FILL' 1;">bolt</span>
</div>
<p class="text-[9px] uppercase tracking-tighter text-outline">Match Score</p>
</div>
</div>
<div class="flex flex-wrap gap-1.5 mb-4">
<span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-secondary-fixed text-secondary">Design Systems</span>
<span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-secondary-fixed text-secondary">React</span>
</div>
<div class="flex justify-between items-center pt-3 border-t border-outline-variant/50">
<div class="flex -space-x-2">
<div class="w-6 h-6 rounded-full border-2 border-white bg-secondary-fixed flex items-center justify-center text-[10px] font-bold text-secondary">MC</div>
</div>
<span class="text-[11px] text-on-surface-variant flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="schedule">schedule</span> 2h ago
                                    </span>
</div>
</div>
<!-- Card 2 -->
<div class="kanban-card p-3 cursor-grab border-l-4 border-l-secondary/40">
<div class="flex justify-between items-start mb-3">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center font-bold text-on-surface-variant">AM</div>
<div>
<p class="font-title-md text-body-sm font-bold">Alex Martinez</p>
<p class="text-on-surface-variant text-[12px]">Senior UX @@ Stripe</p>
</div>
</div>
<div class="flex flex-col items-end">
<div class="flex items-center text-secondary font-bold text-[12px]">
<span>92%</span>
</div>
<p class="text-[9px] uppercase tracking-tighter text-outline">Match Score</p>
</div>
</div>
<div class="flex justify-between items-center pt-3 border-t border-outline-variant/50">
<span class="text-[11px] text-on-surface-variant flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="attach_file">attach_file</span> Portfolio.pdf
                                    </span>
<span class="text-[11px] text-on-surface-variant flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]" data-icon="schedule">schedule</span> 5h ago
                                    </span>
</div>
</div>
</div>
</div>
<!-- Column: Phone Screen -->
<div class="kanban-column flex flex-col h-full kanban-column flex-shrink-0">
<div class="p-4 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="font-semibold text-[13px] text-on-surface">Phone Screen</span>
<span class="bg-surface-container-high text-on-surface-variant px-2 py-0.5 rounded-full text-[10px] font-bold">4</span>
</div>
<button class="material-symbols-outlined text-on-surface-variant hover:text-on-surface transition-colors" data-icon="more_horiz">more_horiz</button>
</div>
<div class="flex-1 overflow-y-auto px-3 pb-4 space-y-3 custom-scrollbar">
<!-- Card 3 -->
<div class="kanban-card p-3 cursor-grab border-l-4 border-l-secondary/40">
<div class="flex justify-between items-start mb-3">
<div class="flex items-center gap-3">
<img alt="Candidate" class="w-10 h-10 rounded-full object-cover" data-alt="A portrait of a professional man with a friendly, intelligent expression, wearing a navy blue polo shirt. He is in a brightly lit, modern co-working space with large windows and lush green plants in the background. The lighting is high-key and airy, creating a professional and approachable light-mode aesthetic." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAaRwggnoSGvzbome6b_A9yRblfmVH7Od1JJ3Ki0GCi9Pv8fFFyEu4kEdqnJqhB77tP7gaMKC3urxp-atSE8XO49NGqA9XDE7T6OrX9tboVSGK_LK0mD0kPu88lMLuawvhH--XEUlBa9ABZNNuOb6bol8tXlQuNAVojWBkgTJO2dg4aPT2IMkom6n58sNSx6uDR38fxYH3KF6nbflmdFDSKCdxsUnVJFT_icGYJ00qyzSEk81XE7vIxKcyg2vjR9Fw7Si3oNu7DtCdU"/>
<div>
<p class="font-title-md text-body-sm font-bold">David Wilson</p>
<p class="text-on-surface-variant text-[12px]">UI Lead @@ Airbnb</p>
</div>
</div>
<div class="flex flex-col items-end">
<div class="flex items-center text-secondary font-bold text-[12px]">
<span>89%</span>
</div>
<p class="text-[9px] uppercase tracking-tighter text-outline">Match Score</p>
</div>
</div>
<div class="bg-surface-container-low p-2 rounded-lg mb-3 flex items-center gap-2">
<span class="material-symbols-outlined text-secondary text-[16px]" data-icon="calendar_today">calendar_today</span>
<span class="text-[11px] font-medium text-secondary">Today at 2:00 PM</span>
</div>
<div class="flex justify-between items-center pt-3 border-t border-outline-variant/50">
<div class="flex -space-x-1">
<img alt="Reviewer" class="w-5 h-5 rounded-full border-2 border-white object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD4YrrGylgOyReYLJTQod8Oc0tW07RIATEsweX9thdzi0d-VfI6d-z38WyXQMfGUDXW9a2HoilYabD5-pQse1SP_JobWVokWRdbhe_Dy2hYtG5Z9j_YqwvbOY0-jh5Xi3AVw1Sc4hEmCVPU4qECEtTquq5vVfwYDIz59l94nN8fDtPZIXlkjjdr06iUvKScT_s_cYRzvzjhvl8TNI7I5eZCasDdLhSmeZ40PQR8LxX4nzxBZHNlNolD4R9uIE9jNYnf2HfJ2gIflQ4Z"/>
</div>
<span class="text-[11px] text-on-surface-variant">15m duration</span>
</div>
</div>
</div>
</div>
<!-- Column: Interview -->
<div class="kanban-column flex flex-col h-full kanban-column flex-shrink-0">
<div class="p-4 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="font-semibold text-[13px] text-on-surface">Interview</span>
<span class="bg-surface-container-high text-on-surface-variant px-2 py-0.5 rounded-full text-[10px] font-bold">5</span>
</div>
<button class="material-symbols-outlined text-on-surface-variant hover:text-on-surface transition-colors" data-icon="more_horiz">more_horiz</button>
</div>
<div class="flex-1 overflow-y-auto px-3 pb-4 space-y-3 custom-scrollbar">
<!-- Card 4 -->
<div class="kanban-card p-3 cursor-grab border-l-4 border-l-indigo-500">
<div class="flex justify-between items-start mb-3">
<div class="flex items-center gap-3">
<img alt="Candidate" class="w-10 h-10 rounded-full object-cover" data-alt="A portrait of a confident Black female tech executive with a bright, welcoming smile. She is wearing a modern cream-colored silk blouse. The background is a sophisticated glass-walled conference room with soft city views. The image has a crisp, professional lighting style and a luxurious corporate feel, perfectly suited for a premium SaaS interface." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDtUTuZcz27dapOlZjXIvrrQ19w7FHpM0q8_DkUgWDyHBCE_UumOdbaTcNKGyJQSa4rWHlauCxuk_vrsKhDUAwOc7V8L9Yui4tBwZkdQlYRirtWfKJXOQ9gCgUUfws5xGXaKy1f9ALdF7AA9U-1kwirtyreTRwmSqOfID93JWkdZ_nuINp_5VFmMxVgwb-4JsdMlQ2Hr1H1zwN6ds02LtXNCObxHq5fhp74PQ-iU3qszhuxbU00qSuPkgz1aN62A2nkN0Z6FFWLPZtX"/>
<div>
<p class="font-title-md text-body-sm font-bold">Elena Rodriguez</p>
<p class="text-on-surface-variant text-[12px]">Senior Designer @@ Uber</p>
</div>
</div>
<div class="flex flex-col items-end">
<div class="flex items-center text-secondary font-bold text-[12px]">
<span>95%</span>
</div>
<p class="text-[9px] uppercase tracking-tighter text-outline">Match Score</p>
</div>
</div>
<div class="flex gap-2 mb-3">
<span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md text-[10px] font-bold flex items-center gap-1 uppercase tracking-wider">
<span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Ready
                                    </span>
<span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">On-site</span>
</div>
<div class="flex justify-between items-center pt-3 border-t border-outline-variant/50">
<div class="flex -space-x-1">
<div class="w-5 h-5 rounded-full bg-pink-100 text-[8px] flex items-center justify-center font-bold">JS</div>
<div class="w-5 h-5 rounded-full bg-blue-100 text-[8px] flex items-center justify-center font-bold">RK</div>
</div>
<span class="text-[11px] text-on-surface-variant">Round 2 scheduled</span>
</div>
</div>
</div>
</div>
<!-- Column: Technical -->
<div class="kanban-column flex flex-col h-full kanban-column flex-shrink-0">
<div class="p-4 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="font-semibold text-[13px] text-on-surface">Technical</span>
<span class="bg-surface-container-high text-on-surface-variant px-2 py-0.5 rounded-full text-[10px] font-bold">3</span>
</div>
<button class="material-symbols-outlined text-on-surface-variant hover:text-on-surface transition-colors" data-icon="more_horiz">more_horiz</button>
</div>
<div class="flex-1 overflow-y-auto px-3 pb-4 space-y-3 custom-scrollbar">
<!-- Card 5 -->
<div class="kanban-card p-3 cursor-grab border-l-4 border-l-purple-500">
<div class="flex justify-between items-start mb-3">
<div class="flex items-center gap-3">
<img alt="Candidate" class="w-10 h-10 rounded-full object-cover" data-alt="A sharp, modern portrait of a male software architect with glasses, looking thoughtfully into the camera. He is wearing a minimalist black turtleneck. The background is an abstract architectural detail with shadows and light play, conveying deep technical expertise and high-end modernism in a light-mode corporate palette." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCNnkr89CUN1J4mt91YTgzz-AbYL_WIIWtd19eXastaLIbIJbO0TuecshpualWirZhaIFKXcTXNiAB8WuaSU0pWTQqGHv-64Wf6FE0JLgRqFaANNbd4CaBowgw8PEpLaY6rZcz2dCk9t5fe5Zk_jEQU56IAZNP0UUzLjooxwkEB4Dm6xn0zL0Ktl5TCEA4qHskT1gTvAMNmgyQ4S_kRiZpnF0aGlCdhO0ojh6iogb-Ea2eyaUbX2Nc8rPCeDUqpDVERGZeEzMReOlV8"/>
<div>
<p class="font-title-md text-body-sm font-bold">James Cooper</p>
<p class="text-on-surface-variant text-[12px]">Design Ops @@ Adobe</p>
</div>
</div>
<div class="flex flex-col items-end">
<div class="flex items-center text-secondary font-bold text-[12px]">
<span>91%</span>
</div>
<p class="text-[9px] uppercase tracking-tighter text-outline">Match Score</p>
</div>
</div>
<div class="flex items-center justify-between p-2 bg-surface-container rounded-lg">
<span class="text-[11px] font-bold text-primary flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-purple-600" data-icon="terminal">terminal</span> Coding Review
                                    </span>
<span class="text-[10px] text-on-surface-variant bg-surface-container px-1.5 py-0.5 rounded border border-outline-variant">Reviewing</span>
</div>
</div>
</div>
</div>
<!-- Column: Offer -->
<div class="kanban-column flex flex-col h-full kanban-column flex-shrink-0">
<div class="p-4 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="font-semibold text-[13px] text-on-surface">Offer</span>
<span class="bg-surface-container-high text-on-surface-variant px-2 py-0.5 rounded-full text-[10px] font-bold">4</span>
</div>
<button class="material-symbols-outlined text-on-surface-variant hover:text-on-surface transition-colors" data-icon="more_horiz">more_horiz</button>
</div>
<div class="flex-1 overflow-y-auto px-3 pb-4 space-y-3 custom-scrollbar">
<!-- Card 6 -->
<div class="kanban-card p-3 cursor-grab border-l-4 border-l-secondary">
<div class="flex justify-between items-start mb-3">
<div class="flex items-center gap-3">
<img alt="Candidate" class="w-10 h-10 rounded-full object-cover" data-alt="A professional portrait of a woman with a confident, visionary gaze, set in a bright and airy modern executive office. She is wearing a tailored navy blazer over a crisp white shirt. The background features blurred glass partitions and soft warm light, creating a sophisticated and clean light-mode aesthetic for a high-end HR application." src="https://lh3.googleusercontent.com/aida-public/AB6AXuB_Q_RwQf-dEKoRcCNjR-orJdvOT_egyi9CWe5V2v8f3Fmw30OCYYPUXzWiU7k0XN3v03S0EfP-2lyj-FIh2qkBst51IxzMnx8Og_E1rmdVoxrZguVjdbEIwnazZ4J1QlCYJy5oezIC4UYkV-cXSyz1-DKRqJQ1Ow7lx1TPsrPKnR5X0fSW5Mnou3CpxQ7R9Cl7Tx4GnNV3OehMDDgyHxCTwUBCA5imCglXnkxsmcNn3hoz6pQCedrxj9NLKJDrYX9flNx6ZulQOr5G"/>
<div>
<p class="font-title-md text-body-sm font-bold">Sophie Bennett</p>
<p class="text-on-surface-variant text-[12px]">Principal @@ Spotify</p>
</div>
</div>
<div class="flex flex-col items-end">
<div class="flex items-center text-secondary font-bold text-[12px]">
<span>96%</span>
</div>
<p class="text-[9px] uppercase tracking-tighter text-outline">Match Score</p>
</div>
</div>
<div class="bg-secondary/10 p-3 rounded-lg border border-secondary/20">
<p class="text-[11px] font-bold text-secondary mb-1">Offer Extended</p>
<div class="w-full bg-secondary/10 h-1.5 rounded-full overflow-hidden">
<div class="bg-secondary h-full w-[75%]"></div>
</div>
<p class="text-[10px] text-secondary mt-1 text-right">Pending signature</p>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Footer (Shared Component) -->
<footer class="w-full py-8 bg-surface border-t border-outline-variant mt-auto">
<div class="flex flex-col md:flex-row justify-between items-center px-container-margin max-w-7xl mx-auto">
<p class="font-body-sm text-body-sm text-on-surface-variant">© 2024 Elements HR Services. All rights reserved.</p>
<div class="flex gap-8 mt-4 md:mt-0">
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-opacity opacity-80 hover:opacity-100" href="{{ route('landing') }}">Privacy Policy</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-opacity opacity-80 hover:opacity-100" href="{{ route('landing') }}">Terms of Service</a>
<a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-opacity opacity-80 hover:opacity-100" href="{{ route('login') }}">Contact Support</a>
</div>
</div>
</footer>
</div>
@endsection
