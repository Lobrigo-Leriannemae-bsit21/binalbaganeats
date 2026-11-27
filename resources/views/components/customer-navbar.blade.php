@inject('cart', 'App\Services\CartService')

@php
    $signedIn = auth()->check();
    $isCustomer = $signedIn && auth()->user()->isCustomer();
    $unread = $isCustomer ? auth()->user()->notifications()->whereNull('read_at')->count() : 0;
@endphp

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top be-navbar">
    <div class="container">
        <x-logo :size="40" :tagline="false" />

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav" aria-controls="mainNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto ms-lg-4 mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>

                @unless ($signedIn)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#how-it-works">How it works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}#features">Why BinalbaganEats</a>
                    </li>
                @endunless

                @if ($isCustomer)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('vendors.*', 'menu.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">Vendors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">My Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('buyforme.*') ? 'active' : '' }}" href="{{ route('buyforme.index') }}">Buy-for-Me</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('suki.*') ? 'active' : '' }}" href="{{ route('suki.index') }}">Suki Points</a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('help') ? 'active' : '' }}" href="{{ route('help') }}">Help</a>
                </li>
            </ul>

            @unless ($signedIn)
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                </div>
            @endunless

            {{-- Signed in as a vendor, rider or admin: only a link to their own portal --}}
            @if ($signedIn && ! $isCustomer)
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ \App\Support\RoleHome::url(auth()->user()) }}" class="btn btn-primary">
                        Go to my {{ strtolower(auth()->user()->role->label()) }} portal
                    </a>
                </div>
            @endif

            @if ($isCustomer)
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('notifications.index') }}" class="position-relative text-dark fs-5"
                       aria-label="Notifications ({{ $unread }} unread)">
                        <i class="bi bi-bell"></i>
                        @if ($unread > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill be-cart-badge">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('cart.index') }}" class="position-relative text-dark fs-5"
                       aria-label="My cart ({{ $cart->count() }} items)">
                        <i class="bi bi-cart3"></i>
                        @if ($cart->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill be-cart-badge">
                                {{ $cart->count() }}
                            </span>
                        @endif
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none text-dark d-flex align-items-center gap-2 p-0"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="be-avatar">{{ auth()->user()->initials }}</span>
                            <span class="fw-semibold">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('account.profile') }}">My profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}">My orders</a></li>
                            <li><a class="dropdown-item" href="{{ route('buyforme.index') }}">Buy-for-Me</a></li>
                            <li><a class="dropdown-item" href="{{ route('suki.index') }}">Suki Points</a></li>
                            <li><a class="dropdown-item" href="{{ route('notifications.index') }}">Notifications</a></li>
                            <li><a class="dropdown-item" href="{{ route('cart.index') }}">My cart</a></li>
                            <li><a class="dropdown-item" href="{{ route('account.settings') }}">Account settings</a></li>
                            <li><a class="dropdown-item" href="{{ route('help') }}">Help</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-1"></i> Log out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</nav>