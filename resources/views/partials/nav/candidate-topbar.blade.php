@php
    $defaultHeaderLeft = view('partials.nav.candidate-header-search-default')->render();
@endphp
<header class="fixed top-0 right-0 w-full md:w-[calc(100%-280px)] h-16 bg-surface/80 backdrop-blur-lg border-b border-outline-variant z-40">
<div class="flex justify-between items-center h-full px-8 max-w-[1440px] mx-auto gap-4">
<div class="flex items-center flex-1 min-w-0">
{!! $__env->yieldPushContent('candidate-header-left', $defaultHeaderLeft) !!}
</div>
<div class="flex items-center gap-4 flex-shrink-0">
@stack('candidate-header-actions')
@include('partials.nav.profile-dropdown')
</div>
</div>
</header>
