<footer class="mt-16 py-6">
    <div class="divider mb-6"></div>
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 px-4">
        <p class="text-[13px] text-on-surface-variant">© {{ date('Y') }} Elements HR. All rights reserved.</p>
        <div class="flex gap-6">
            <a class="text-[12px] text-on-surface-variant hover:text-secondary transition-colors" href="{{ route('landing') }}">Privacy Policy</a>
            <a class="text-[12px] text-on-surface-variant hover:text-secondary transition-colors" href="{{ route('landing') }}">Terms of Service</a>
            <a class="text-[12px] text-on-surface-variant hover:text-secondary transition-colors" href="{{ route('login') }}">Contact Support</a>
        </div>
    </div>
</footer>
