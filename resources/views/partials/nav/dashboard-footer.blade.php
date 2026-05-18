<footer class="mt-16 py-8 border-t border-outline-variant">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 px-4">
        <p class="font-body-sm text-body-sm text-on-surface-variant">© {{ date('Y') }} Elements HR. All rights reserved.</p>
        <div class="flex gap-6">
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('landing') }}">Terms of Service</a>
            <a class="font-label-caps text-label-caps text-on-surface-variant hover:text-primary transition-colors" href="{{ route('login') }}">Contact Support</a>
        </div>
    </div>
</footer>
