<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,500&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/js/app.js'])
    @stack('styles')
</head>
<body class="@yield('body_class', 'role-customer') d-flex flex-column min-vh-100">

    <x-customer-navbar />
    <x-flash />

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="be-footer text-center py-4">
        &copy; {{ date('Y') }} BinalbaganEats &middot; Food made closer
    </footer>

    @stack('scripts')
</body>
</html>