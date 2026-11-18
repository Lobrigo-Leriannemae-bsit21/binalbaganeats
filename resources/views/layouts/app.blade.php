<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BinalbaganEats — Local food. On the way.')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/binalbaganeats-theme.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <x-navbar />

    <main class="flex-grow-1">
        <div class="container py-3">
            <x-alert />
        </div>
        @yield('content')
    </main>

    <footer class="bg-be-primary text-white py-4 mt-5">
        <div class="container">
            <div class="row align-items-center gy-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold fs-5">BinalbaganEats</span>
                        <span class="badge bg-be-sage text-be-primary">Barangay San Pedro & Binalbagan</span>
                    </div>
                    <p class="mb-0 small text-white-50 mt-1">Local food. On the way. Connecting local vendors, riders, and customers.</p>
                </div>
                <div class="col-md-6 text-md-end small text-white-50">
                    &copy; {{ date('Y') }} BinalbaganEats Capstone Prototype — CHMSU-Binalbagan
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/prototype-state.js') }}"></script>
    @stack('scripts')
</body>
</html>