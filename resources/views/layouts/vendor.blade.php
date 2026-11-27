<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }} Vendor Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,500&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/js/app.js'])
    @stack('styles')
</head>
<body class="role-vendor">

@php
    $user = auth()->user();
    $store = $user->vendorProfile;

    $mainLinks = [
        ['vendor.dashboard', 'bi-speedometer2', 'Dashboard', 'vendor.dashboard'],
        ['vendor.store.edit', 'bi-shop', 'My Store', 'vendor.store.*'],
    ];

    $accountLinks = [
        ['vendor.account.profile', 'bi-person', 'Profile', 'vendor.account.profile*'],
        ['vendor.account.password', 'bi-shield-lock', 'Change password', 'vendor.account.password*'],
        ['vendor.account.settings', 'bi-gear', 'Settings', 'vendor.account.settings*'],
    ];
@endphp

<div class="be-shell">

    {{-- Sidebar (a slide-in menu on phones and tablets) --}}
    <div class="be-sidebar-col">
        <div class="offcanvas-lg offcanvas-start be-sidebar" tabindex="-1" id="vendorSidebar" aria-labelledby="vendorSidebarLabel">
            <div class="offcanvas-header d-lg-none">
                <span class="fw-semibold" id="vendorSidebarLabel">Vendor menu</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        data-bs-target="#vendorSidebar" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body p-0">
                <div class="be-side-inner">
                    <div class="be-side-brand">
                        <x-logo :size="34" :tagline="false" :href="route('vendor.dashboard')" />
                        <div class="be-side-role">Vendor Portal</div>
                    </div>

                    <div class="be-side-store">
                        <div class="fw-semibold text-truncate">{{ $store->business_name }}</div>
                        <span class="be-badge {{ $store->is_available ? 'be-badge-success' : 'be-badge-danger' }}">
                            {{ $store->is_available ? 'Open' : 'Closed' }}
                        </span>
                    </div>

                    <nav class="be-side-nav" aria-label="Vendor navigation">
                        @foreach ($mainLinks as [$routeName, $icon, $label, $pattern])
                            <a href="{{ route($routeName) }}"
                               class="be-side-link {{ request()->routeIs($pattern) ? 'active' : '' }}"
                               @if (request()->routeIs($pattern)) aria-current="page" @endif>
                                <i class="bi {{ $icon }}"></i> {{ $label }}
                            </a>
                        @endforeach

                        <div class="be-side-heading">Account</div>

                        @foreach ($accountLinks as [$routeName, $icon, $label, $pattern])
                            <a href="{{ route($routeName) }}"
                               class="be-side-link {{ request()->routeIs($pattern) ? 'active' : '' }}"
                               @if (request()->routeIs($pattern)) aria-current="page" @endif>
                                <i class="bi {{ $icon }}"></i> {{ $label }}
                            </a>
                        @endforeach

                        <form method="POST" action="{{ route('vendor.logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="be-side-link">
                                <i class="bi bi-box-arrow-right"></i> Log out
                            </button>
                        </form>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- Main area --}}
    <div class="be-main">
        <header class="be-topbar">
            <button class="btn btn-outline-secondary d-lg-none" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#vendorSidebar"
                    aria-controls="vendorSidebar" aria-label="Open menu">
                <i class="bi bi-list fs-5"></i>
            </button>

            <div class="fw-semibold text-truncate me-auto">{{ $store->business_name }}</div>

            <div class="dropdown">
                <button class="btn btn-link text-decoration-none text-dark d-flex align-items-center gap-2 p-0"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="be-avatar">{{ $user->initials }}</span>
                    <span class="fw-semibold d-none d-sm-inline">{{ $user->name }}</span>
                    <i class="bi bi-chevron-down small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('vendor.account.profile') }}">My profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('vendor.account.settings') }}">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('vendor.logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-1"></i> Log out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <div class="be-content">
            <div class="be-content-inner">
                @foreach (['success' => 'success', 'error' => 'danger', 'status' => 'info'] as $key => $type)
                    @if (session($key))
                        <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                            {{ session($key) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                @endforeach

                @yield('content')
            </div>
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>