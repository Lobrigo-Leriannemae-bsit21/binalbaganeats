@extends('layouts.app')

@section('title', 'Redeem Suki Points')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('suki.index') }}">Suki Points</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Redeem</li>
                </ol>
            </nav>

            <h1 class="h3 mb-4">Redeem your points</h1>

            <div class="be-card p-4 mb-4 text-center">
                <p class="text-muted small text-uppercase mb-1">Available balance</p>
                <p class="display-5 fw-bold mb-0">{{ number_format($balance) }} <span class="fs-5 text-muted">points</span></p>
                <p class="text-muted mb-0">≈ ₱{{ number_format($balance * $rules['value_pesos'], 2) }}</p>
            </div>

            <div class="be-card p-4 mb-4">
                <h2 class="h5 mb-3">Using points on your cart</h2>

                @if ($cartEmpty)
                    <p class="text-muted">Your cart is empty. Add food from a vendor first, and you’ll see how many points you can use.</p>
                    <a href="{{ route('vendors.index') }}" class="btn btn-primary">Browse vendors</a>

                @elseif ($balance < $rules['min_redeem'])
                    <p class="mb-2">You need at least <strong>{{ $rules['min_redeem'] }} points</strong> to redeem. You have {{ number_format($balance) }}.</p>
                    <p class="text-muted small mb-3">Keep ordering. Points are added after each delivery.</p>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">Back to my cart</a>

                @elseif (! $canRedeem)
                    <p class="mb-2">Your cart (₱{{ number_format($subtotal, 2) }}) is too small to use points right now.</p>
                    <p class="text-muted small mb-3">Points can cover up to {{ $rules['max_discount_percent'] }}% of the food subtotal, and at least {{ $rules['min_redeem'] }} points must be used.</p>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">Back to my cart</a>

                @else
                    <p class="mb-1">Food subtotal: <strong>₱{{ number_format($subtotal, 2) }}</strong></p>
                    <p class="mb-3">
                        You can use up to <strong>{{ number_format($max) }} points</strong>
                        (₱{{ number_format($max * $rules['value_pesos'], 2) }} off) on this order.
                    </p>
                    <a href="{{ route('checkout.show') }}" class="btn btn-primary">
                        Go to checkout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <p class="small text-muted mt-3 mb-0">You choose how many points to apply on the checkout page.</p>
                @endif
            </div>

            <div class="be-inset p-3 small">
                <strong>Remember:</strong> use at least {{ $rules['min_redeem'] }} points, up to {{ $rules['max_discount_percent'] }}% of the food subtotal.
                Delivery fees can’t be discounted, and points you use are held until the order is delivered.
                If the order is cancelled or declined, the points are returned.
            </div>
        </div>
    </div>
</div>
@endsection