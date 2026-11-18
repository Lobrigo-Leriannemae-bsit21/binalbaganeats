<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-be-primary fs-4" href="{{ route('home') }}">
            <img src="{{ asset('images/binalbaganeats-scooter-mark.png') }}" alt="BinalbaganEats Logo" class="be-logo-navbar">
            <span>Binalbagan<span class="text-be-accent">eats</span></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="publicNavbar">
            <ul class="navbar-nav me-auto ms-lg-4 gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ request()->routeIs('home') ? 'text-be-accent' : 'text-be-primary' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold {{ request()->routeIs('public.vendors*') ? 'text-be-accent' : 'text-be-primary' }}" href="{{ route('public.vendors') }}">Browse Vendors</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                @auth
                    <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="btn btn-be-primary px-4">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-be-primary px-4">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-be-accent px-4">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>