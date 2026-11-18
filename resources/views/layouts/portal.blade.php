<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') — BinalbaganEats</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/binalbaganeats-theme.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        {{-- Role Sidebar --}}
        <x-sidebar :role="$role ?? (auth()->user()->role ?? 'customer')" />

        {{-- Main Workspace --}}
        <div class="flex-grow-1 d-flex flex-column min-vh-100" style="min-width: 0;">
            {{-- Top Header Bar --}}
            <header class="bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between sticky-top">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-be-primary d-lg-none" type="button" id="sidebarToggle">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold text-be-primary">@yield('page-title', 'Dashboard')</h5>
                        <small class="text-be-muted">@yield('page-subtitle', 'Local food. On the way.')</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-be-sage text-be-primary px-3 py-2 text-capitalize">
                        <i class="bi bi-person-badge me-1"></i> {{ auth()->user()->role ?? 'Guest' }}
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle text-be-primary"></i>
                            <span class="d-none sm-inline fw-semibold">{{ auth()->user()->name ?? 'User' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route(($role ?? auth()->user()->role ?? 'customer') . '.profile') }}">
                                    <i class="bi bi-gear me-2"></i> Profile Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="p-4 flex-grow-1">
                <x-alert />
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/prototype-state.js') }}"></script>
    @stack('scripts')
</body>
</html>