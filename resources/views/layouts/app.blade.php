<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ config('app.name') }} | @yield('title')</title>
    @if (file_exists(public_path('images/logo.webp')))
        <link rel="icon" type="image/webp" href="{{ asset('images/logo.webp') }}">
    @endif

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    {{-- Base + Design System --}}
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/design-system.css') }}" />

    {{-- Theme variables — loaded AFTER design-system.css so :root (light) wins the cascade --}}
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}" />

    {{-- Per-page CSS --}}
    @hasSection('page-css')
        <link rel="stylesheet" href="{{ asset('css/' . trim($__env->yieldContent('page-css'))) }}" />
    @endif

    {{-- Per-page Tailwind config --}}
    @hasSection('tailwind-config')
        <script src="{{ asset('js/' . trim($__env->yieldContent('tailwind-config'))) }}"></script>
    @endif

    {{-- Component overrides — loaded LAST so they win over page CSS --}}
    <link rel="stylesheet" href="{{ asset('css/components.css') }}" />

    @stack('styles')
    @stack('head-scripts')
</head>

<body class="@yield('body-class') antialiased">
    @yield('content')
    @stack('scripts')

    {{-- Theme JS (light-only; no toggle) --}}
    <script src="{{ asset('js/theme.js') }}"></script>
</body>

</html>
