@extends('layouts.app')

@section('title', 'Food made closer')

@section('content')

{{-- Hero --}}
<section class="be-hero py-5">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <p class="text-uppercase fw-semibold mb-2" style="color: var(--be-amber); letter-spacing: .08em;">
                    Binalbagan, Negros Occidental
                </p>
                <h1 class="fw-bold mb-3">Cravings? We've got you covered!</h1>
                <p class="lead mb-4">
                    Order from your favorite local food vendors, earn Suki Points, and get
                    your meal delivered right to your door. Food made closer to you.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-amber btn-lg">Create an account</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Log in</a>
                    @endguest
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-amber btn-lg">Go to my dashboard</a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="BinalbaganEats emblem" class="be-hero-emblem">
            </div>
        </div>
    </div>
</section>

{{-- How it works --}}
<section id="how-it-works" class="py-5">
    <div class="container">
        <h2 class="text-center mb-2">How it works</h2>
        <p class="text-center text-muted mb-5">From craving to doorstep in four simple steps.</p>

        <div class="row g-4 text-center">
            @foreach ([
                ['bi-search', 'Discover', 'Browse local vendors and their menus.'],
                ['bi-cart3', 'Order', 'Add your favorites to the cart and check out.'],
                ['bi-wallet2', 'Pay', 'Choose Cash on Delivery or GCash.'],
                ['bi-geo-alt', 'Track', 'Follow your order until it arrives.'],
            ] as [$icon, $title, $text])
                <div class="col-6 col-lg-3">
                    <div class="be-step-icon mx-auto mb-3"><i class="bi {{ $icon }}"></i></div>
                    <h3 class="h6 mb-1">{{ $title }}</h3>
                    <p class="text-muted small mb-0">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Features --}}
<section id="features" class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <h2 class="text-center mb-5">Why BinalbaganEats</h2>

        <div class="row g-4">
            @foreach ([
                ['bi-shop', 'Support local business', 'Every order helps a food business in your own community.'],
                ['bi-gift', 'Suki Points rewards', 'Earn points on your orders and redeem them for discounts.'],
                ['bi-bag-heart', 'Buy-for-Me service', 'Request items from other establishments and we will get them for you.'],
                ['bi-truck', 'Local delivery', 'Delivery fees are set by zone, so you know the cost before you order.'],
            ] as [$icon, $title, $text])
                <div class="col-sm-6 col-lg-3">
                    <div class="be-card p-4 h-100">
                        <div class="be-step-icon mb-3"><i class="bi {{ $icon }}"></i></div>
                        <h3 class="h6">{{ $title }}</h3>
                        <p class="text-muted small mb-0">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection