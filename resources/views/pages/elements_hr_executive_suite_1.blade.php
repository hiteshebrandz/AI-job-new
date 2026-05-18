@extends('layouts.app')

@section('title', 'Executive Suite')

@section('body-class', 'bg-background text-on-surface selection:bg-secondary-fixed selection:text-on-secondary-fixed overflow-x-hidden')

@section('page-css', 'elements_hr_executive_suite_1.css')

@section('tailwind-config', 'tailwind-config-default.js')

@section('content')
@include('partials.nav.public-header')
<!-- Hero Section -->
<section class="relative pt-32 pb-24 hero-gradient overflow-hidden min-h-[921px] flex items-center">
<div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;"></div>
<div class="max-w-7xl mx-auto px-container-margin relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
<div class="space-y-8">
<div class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-md">
<span class="text-white font-label-caps text-label-caps uppercase tracking-widest">Next-Gen HR Tech</span>
</div>
<h1 class="font-display-lg text-display-lg text-white max-w-xl">Precision Engineering for Modern Talent.</h1>
<p class="font-body-md text-body-md text-white/80 max-w-md">Elements HR uses advanced AI-driven logic to bridge the gap between world-class talent and global executive opportunities.</p>
<div class="flex flex-col sm:flex-row gap-4">
<button class="px-8 py-4 bg-white text-secondary font-title-md text-title-md rounded-xl hover:shadow-lg transition-all active:scale-[0.98]">
                        Find your Dream Job
                    </button>
<button class="px-8 py-4 border border-white/30 text-white font-title-md text-title-md rounded-xl hover:bg-white/10 backdrop-blur-md transition-all active:scale-[0.98]">
                        Hire Top Talent
                    </button>
</div>
</div>
<div class="relative">
<!-- AI Matching Glassmorphism Card -->
<div class="glass-card rounded-[24px] p-card-padding shadow-2xl relative overflow-hidden">
<div class="flex items-center justify-between mb-8">
<div>
<p class="font-label-caps text-label-caps text-secondary font-bold uppercase mb-1">Live Engine</p>
<h3 class="font-title-md text-title-md text-on-surface">AI-Matching in Progress</h3>
</div>
<span class="material-symbols-outlined text-secondary text-4xl animate-pulse">model_training</span>
</div>
<div class="space-y-6">
<!-- Candidate 1 -->
<div class="flex items-center gap-4 bg-white/50 p-4 rounded-xl border border-white/50">
<div class="h-12 w-12 rounded-lg bg-secondary-fixed flex items-center justify-center">
<span class="material-symbols-outlined text-secondary">person</span>
</div>
<div class="flex-1">
<p class="font-title-md text-body-md font-bold">Principal Product Designer</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Matching with 'TechFlow Systems'</p>
</div>
<div class="text-right">
<span class="text-secondary font-bold font-title-md">98%</span>
<div class="w-16 h-1.5 bg-surface-variant rounded-full mt-1 overflow-hidden">
<div class="bg-gradient-to-r from-secondary to-purple-500 h-full w-[98%]"></div>
</div>
</div>
</div>
<!-- Candidate 2 -->
<div class="flex items-center gap-4 bg-white/50 p-4 rounded-xl border border-white/50">
<div class="h-12 w-12 rounded-lg bg-surface-container-high flex items-center justify-center">
<span class="material-symbols-outlined text-on-surface-variant">person</span>
</div>
<div class="flex-1">
<p class="font-title-md text-body-md font-bold">VP of Engineering</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Matching with 'Quantix AI'</p>
</div>
<div class="text-right">
<span class="text-secondary font-bold font-title-md">92%</span>
<div class="w-16 h-1.5 bg-surface-variant rounded-full mt-1 overflow-hidden">
<div class="bg-gradient-to-r from-secondary to-purple-500 h-full w-[92%]"></div>
</div>
</div>
</div>
</div>
<div class="mt-8 flex justify-center">
<div class="flex -space-x-3">
<img class="w-10 h-10 rounded-full border-2 border-white object-cover" data-alt="A professional woman smiling in a bright studio environment, representing a successful candidate profile in a high-end tech HR platform. The image is clean, sharp, and uses soft professional lighting." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBCA-fw-qXGvxC3TYhIAEGCrGonY87FXIc55rfRnkCUMQTkQuIPMv73WerlSScb_VTHYhp4mhR_HiNB7NIwyJ_NwSayL3s5BwiQoAsfxG8TIsY7ygeT46H5-5akCpxlNzY_G4TrWxrdTczpfxiQrHkUSMLF7ba4xXv4LW-oU2AV87HiLIC57GP9AhaegQNRgHXq8_3_8kUKu930OfSaYGZk8pIfPiTNSyYzEuNqBsoJcwUU2yx4f5evEar47quENcnwlGwMut_Qoxyl"/>
<img class="w-10 h-10 rounded-full border-2 border-white object-cover" data-alt="A smiling young professional male in a smart-casual blazer against a blurred modern architectural background. The shot is high-definition, with natural daylight creating a warm, approachable, and successful corporate vibe." src="https://lh3.googleusercontent.com/aida-public/AB6AXuD1-NL0qLNAJcsFb2STiHfLfTe5EBsMorLEyvd471LabDTXSh8o3L6AaDaCsHQQvcHyMOqKAb8u8WO-FXb3ZqNddVP0vS3vhdGiKPPDZTZHeZna9KaZiF-sBAIkcXr7fGaNd8G1wS3aJRnsyPm1sodukFG_HSUUO0cszj6bubRklrge0uEy0Q_GeecFLLJRHP4Dbr_Cy6I6RC_Eel_3beaZfcFnMWnqKC0d2bazwxTfRihfCOs8u4yELoN5zKDd-andH2PWvhWt5jCa"/>
<img class="w-10 h-10 rounded-full border-2 border-white object-cover" data-alt="A middle-aged professional woman with glasses, looking confident and poised. The setting is a bright, minimalist coworking space with soft depth of field, reflecting a high-level executive recruitment atmosphere." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAmakHeWblGv1-PYrkyjH-6iIVNsACNADTljYX-24F-4PKAkR3ICtU7iyAjEDBp7Rlt_xrHH1QBkor23forrSYn4otNqY6ZhD4TqJ64HXszb-6LckrvveuAux2WGOhpD9xvSv4TwxCpVr0n0RRjX_ySFZtLGBt0rIXd1jQGcgRDIpDGofJP3lSCC7012nAJOrtRPlPZKg_32TU4e4dk8mONgVKRtzPfv3U96m0ftJ6cYVmCrAIV5Jpxmo65xslSby_U0Tsw-e9XPnT7"/>
<div class="w-10 h-10 rounded-full border-2 border-white bg-secondary flex items-center justify-center text-white text-xs font-bold">+24k</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Trusted By -->
<section class="py-12 bg-surface border-b border-outline-variant">
<div class="max-w-7xl mx-auto px-container-margin">
<p class="text-center font-label-caps text-label-caps text-on-surface-variant uppercase mb-8 tracking-widest">Trusted by Global Leaders</p>
<div class="flex flex-wrap justify-center items-center gap-12 md:gap-24 opacity-50 grayscale hover:grayscale-0 transition-all">
<span class="font-headline-lg font-extrabold text-primary tracking-tighter">VOLVO</span>
<span class="font-headline-lg font-extrabold text-primary tracking-tighter">ORACLE</span>
<span class="font-headline-lg font-extrabold text-primary tracking-tighter">STRIPE</span>
<span class="font-headline-lg font-extrabold text-primary tracking-tighter">AIRBNB</span>
<span class="font-headline-lg font-extrabold text-primary tracking-tighter">ADOBE</span>
</div>
</div>
</section>
<!-- How it Works Section -->
<section class="py-24 bg-white">
<div class="max-w-7xl mx-auto px-container-margin">
<div class="text-center mb-16">
<h2 class="font-headline-lg text-headline-lg text-primary mb-4">Precision Workflow</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mx-auto">Our three-step proprietary engine ensures the highest signal-to-noise ratio in executive search.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<!-- Step 1 -->
<div class="p-card-padding rounded-2xl bg-surface-container-low border border-outline-variant hover:border-secondary transition-colors group">
<div class="h-14 w-14 rounded-xl bg-secondary-fixed flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-secondary text-3xl">upload_file</span>
</div>
<h3 class="font-title-md text-title-md mb-3">01. Smart Upload</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Drop your resume or job description. Our system accepts all formats and begins immediate analysis.</p>
</div>
<!-- Step 2 -->
<div class="p-card-padding rounded-2xl bg-surface-container-low border border-outline-variant hover:border-secondary transition-colors group">
<div class="h-14 w-14 rounded-xl bg-secondary-fixed flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-secondary text-3xl">analytics</span>
</div>
<h3 class="font-title-md text-title-md mb-3">02. Semantic Parsing</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">We extract more than just keywords. Our AI understands skills, cultural fit, and professional trajectory.</p>
</div>
<!-- Step 3 -->
<div class="p-card-padding rounded-2xl bg-surface-container-low border border-outline-variant hover:border-secondary transition-colors group">
<div class="h-14 w-14 rounded-xl bg-secondary-fixed flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-secondary text-3xl">auto_awesome</span>
</div>
<h3 class="font-title-md text-title-md mb-3">03. Neural Matching</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Receive a curated list of matches with detailed compatibility scoring and predictive performance data.</p>
</div>
</div>
</div>
</section>
<!-- Why Choose Us -->
<section class="py-24 bg-surface-container-lowest">
<div class="max-w-7xl mx-auto px-container-margin">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
<div class="grid grid-cols-2 gap-4">
<div class="space-y-4">
<div class="aspect-square rounded-2xl overflow-hidden shadow-lg">
<img class="w-full h-full object-cover" data-alt="A diverse team of high-level professionals collaborating around a large touchscreen display in a futuristic, sunlit corporate boardroom. The aesthetic is clean, sophisticated, and heavily focused on the collaboration between human intelligence and technology." src="https://lh3.googleusercontent.com/aida-public/AB6AXuB-FI6XULm2--YHnsWlVB8sUjbfoptgyZmzICuR7gRpjSwcVBSS_KMeJ4y6mrjKlcW3nZ9ALnBQFOh2hwDj8-YHoLo8xfda0Jh3eCTt4KOK83T3pduMpl1zUp1amy2VMiSCgi2QDM7CtrXZ7Nm16lwh4pwjyM9iYQnlDOlaS_l87QtIDkPWf-xmkJyIlQ_ECHhMYXrB3jdNUBP7N65WMgW51r0XluRN1K_SbZ5oKQrJaLlJIBtOO5cYwjLcPcVI_mfyqN9Fe_cTs93y"/>
</div>
<div class="aspect-[4/3] rounded-2xl bg-secondary-container p-6 text-white flex flex-col justify-end">
<p class="font-headline-lg text-headline-lg font-bold">12ms</p>
<p class="font-label-caps text-label-caps opacity-80 uppercase">Match Latency</p>
</div>
</div>
<div class="space-y-4 pt-8">
<div class="aspect-[3/4] rounded-2xl overflow-hidden shadow-lg">
<img class="w-full h-full object-cover" data-alt="Close-up of a high-resolution data dashboard on a tablet being held by a professional in a navy suit. The screen shows intricate neural network diagrams and success metrics, reinforcing the theme of data-driven human resource management." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBwLvAUJXd2K_M-vS7_EOmmkczHh3JMm7SeRWmspnxP2hgGrhpFgV9L2_X1_svMIr07kCVHnwLPvgbSd9-Ms9QZNTYcgSoIRX5cGuTDDGvo_mWV4UfGUWHzzSLHVEGy4SWHdkVMgCXpdWh-E6Gm7lFRp3KM8LzL1F0A7O0Gu11peVWmVfdKXC0EQvNX9x1w9FnEo5WxFODrVmzK-y1sdi6pXdkqx3GCP7elEuMPCmSKNSD5fDZdG_imL69YVQnt1IsmiXspP2NWoxG3"/>
</div>
<div class="aspect-square rounded-2xl bg-primary-container p-6 text-white flex flex-col justify-end">
<p class="font-headline-lg text-headline-lg font-bold">94%</p>
<p class="font-label-caps text-label-caps opacity-80 uppercase">Retention Rate</p>
</div>
</div>
</div>
<div class="space-y-8">
<h2 class="font-headline-lg text-headline-lg text-primary">Why Industry Leaders Choose Elements.</h2>
<p class="font-body-md text-body-md text-on-surface-variant">We don't just find employees; we engineer success. Our platform combines deep-learning algorithms with human-centric design to deliver talent that sticks.</p>
<ul class="space-y-6">
<li class="flex items-start gap-4">
<div class="h-6 w-6 rounded-full bg-secondary-fixed flex items-center justify-center shrink-0 mt-1">
<span class="material-symbols-outlined text-secondary text-sm" style="font-variation-settings: 'FILL' 1;">check</span>
</div>
<div>
<h4 class="font-title-md text-body-md font-bold">Executive Grade Security</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Enterprise-level data protection and privacy compliance globally.</p>
</div>
</li>
<li class="flex items-start gap-4">
<div class="h-6 w-6 rounded-full bg-secondary-fixed flex items-center justify-center shrink-0 mt-1">
<span class="material-symbols-outlined text-secondary text-sm" style="font-variation-settings: 'FILL' 1;">check</span>
</div>
<div>
<h4 class="font-title-md text-body-md font-bold">Diversity-First Logic</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Algorithmic bias neutralization for a truly equitable hiring process.</p>
</div>
</li>
<li class="flex items-start gap-4">
<div class="h-6 w-6 rounded-full bg-secondary-fixed flex items-center justify-center shrink-0 mt-1">
<span class="material-symbols-outlined text-secondary text-sm" style="font-variation-settings: 'FILL' 1;">check</span>
</div>
<div>
<h4 class="font-title-md text-body-md font-bold">Predictive Analytics</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Forecast long-term performance and cultural alignment before the first interview.</p>
</div>
</li>
</ul>
</div>
</div>
</div>
</section>
@include('partials.nav.public-footer')
@endsection
