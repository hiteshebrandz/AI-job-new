<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Elements HR | @yield('title')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}"/>
    @hasSection('page-css')
        <link rel="stylesheet" href="{{ asset('css/' . trim($__env->yieldContent('page-css'))) }}"/>
    @endif
    @hasSection('tailwind-config')
        <script src="{{ asset('js/' . trim($__env->yieldContent('tailwind-config'))) }}"></script>
    @endif
    @stack('styles')
    @stack('head-scripts')
</head>
<body class="@yield('body-class')">
    @yield('content')
    @stack('scripts')
</body>
</html>
