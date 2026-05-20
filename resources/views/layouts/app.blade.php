<!DOCTYPE html>
<html class="dark" lang="en" data-theme="dark">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Elements HR | @yield('title')</title>
    <script>
        // Inline theme init — prevents flash of wrong theme
        (function () {
            try {
                var stored = localStorage.getItem('elements-theme');
                if (stored === 'light') {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
            } catch (e) {}
        })();
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}"/>
    @hasSection('page-css')
        <link rel="stylesheet" href="{{ asset('css/' . trim($__env->yieldContent('page-css'))) }}"/>
    @endif
    @hasSection('tailwind-config')
        <script src="{{ asset('js/' . trim($__env->yieldContent('tailwind-config'))) }}"></script>
    @endif
    @stack('styles')
    @stack('head-scripts')
</head>
<body class="@yield('body-class') antialiased">
    @yield('content')
    @stack('scripts')
    <script>
        // Global theme toggle handler (called by toggle buttons anywhere on page)
        function toggleTheme() {
            var html = document.documentElement;
            var isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark');
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('elements-theme', 'light');
            } else {
                html.classList.add('dark');
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('elements-theme', 'dark');
            }
        }
        // Update all theme toggle icons on page
        document.addEventListener('DOMContentLoaded', function () {
            var icons = document.querySelectorAll('[data-theme-icon]');
            function updateIcons() {
                var dark = document.documentElement.classList.contains('dark');
                icons.forEach(function (el) { el.textContent = dark ? 'light_mode' : 'dark_mode'; });
            }
            updateIcons();
            icons.forEach(function (btn) {
                btn.addEventListener('click', function () { toggleTheme(); updateIcons(); });
            });
        });
    </script>
</body>
</html>
