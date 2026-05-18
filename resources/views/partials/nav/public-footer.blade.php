<footer class="bg-surface border-t border-outline-variant py-16">
    <div class="max-w-7xl mx-auto px-container-margin">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-12">
            <div>
                <a href="{{ route('landing') }}">
                    <h2 class="font-headline-lg text-headline-lg font-bold text-primary mb-2">Elements HR</h2>
                </a>
                <p class="font-body-sm text-body-sm text-on-surface-variant max-w-xs">The executive suite for modern human resource management and talent matching.</p>
            </div>
            <div class="flex flex-wrap gap-8">
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-secondary transition-colors" href="{{ route('suite.one') }}">Solutions</a>
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-secondary transition-colors" href="{{ route('admin.analytics') }}">Intelligence</a>
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-secondary transition-colors" href="{{ route('landing') }}">Company</a>
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-secondary transition-colors" href="{{ route('login') }}">Careers</a>
            </div>
        </div>
        <div class="flex flex-col md:flex-row justify-between items-center pt-8 border-t border-outline-variant gap-4">
            <p class="font-body-sm text-body-sm text-on-surface-variant">© {{ date('Y') }} Elements HR Services. All rights reserved.</p>
            <div class="flex gap-6">
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Terms of Service</a>
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('sitemap') }}">Site Map</a>
                <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('login') }}">Contact Support</a>
            </div>
        </div>
    </div>
</footer>
