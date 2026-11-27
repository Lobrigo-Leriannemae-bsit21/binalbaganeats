@extends('layouts.app')

@section('title', 'Suki Points')

@section('content')
<div class="container py-4 py-lg-5">

    <h1 class="h3 mb-4">Suki Points</h1>

    <div class="be-points-hero p-4 p-md-5 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <p class="small text-uppercase mb-2 opacity-75">Your points balance</p>
                <p class="be-points-number mb-2">{{ number_format($balance) }} <span>points</span></p>
                <p class="fs-5 mb-0">≈ ₱{{ number_format($balance * $rules['value_pesos'], 2) }} in discounts</p>
            </div>
            <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                <a href="{{ route('suki.history') }}" class="btn btn-light">View history</a>
                <a href="{{ route('suki.redeem') }}" class="btn btn-amber">Redeem points</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Pending delivery', $pending, 'Earned on orders still on their way'],
            ['On hold', $held, 'Reserved by orders not yet delivered'],
            ['Total earned', $lifetime, 'Points from delivered orders'],
            ['Points used', $used, 'Redeemed on delivered orders'],
        ] as [$label, $value, $hint])
            <div class="col-6 col-lg-3">
                <div class="be-card p-3 h-100">
                    <p class="text-muted small text-uppercase mb-1">{{ $label }}</p>
                    <p class="h3 fw-bold mb-1">{{ number_format($value) }}</p>
                    <p class="small text-muted mb-0">{{ $hint }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="be-card p-4 h-100">
                <h2 class="h5 mb-3">How Suki Points work</h2>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-bag-check text-success"></i>
                        <span>Earn <strong>1 point</strong> for every <strong>₱{{ number_format($rules['earn_peso_per_point']) }}</strong> of food in your order (delivery fee not included).</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-truck text-success"></i>
                        <span>Points become spendable once your order is <strong>delivered</strong>. Cancelled orders earn nothing.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-coin text-success"></i>
                        <span><strong>1 point = ₱{{ rtrim(rtrim(number_format($rules['value_pesos'], 2), '0'), '.') }}</strong> discount at checkout.</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-percent text-success"></i>
                        <span>Use at least <strong>{{ $rules['min_redeem'] }} points</strong>, covering up to <strong>{{ $rules['max_discount_percent'] }}%</strong> of the food subtotal. Delivery fees can’t be discounted.</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-infinity text-success"></i>
                        <span>Your points never expire.</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="be-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Recent activity</h2>
                    @if ($recent->isNotEmpty())
                        <a href="{{ route('suki.history') }}" class="small fw-semibold">See all</a>
                    @endif
                </div>

                @forelse ($recent as $transaction)
                    <div class="d-flex justify-content-between align-items-center gap-2 py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                        <div>
                            <div class="fw-semibold">
                                @if ($transaction->order)
                                    <a href="{{ route('orders.show', $transaction->order) }}" class="text-dark text-decoration-none">{{ $transaction->order->order_number }}</a>
                                @else
                                    Order
                                @endif
                            </div>
                            <div class="small text-muted">{{ $transaction->created_at->format('M j, Y g:i A') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="{{ $transaction->signedPoints() >= 0 ? 'be-point-plus' : 'be-point-minus' }}">
                                {{ $transaction->signedPoints() >= 0 ? '+' : '−' }}{{ number_format(abs($transaction->signedPoints())) }}
                            </div>
                            <span class="be-badge {{ $transaction->badgeClass() }}">{{ $transaction->statusLabel() }}</span>
                        </div>
                    </div>
                @empty
                    <x-empty-state icon="bi-gift" title="No points yet"
                                   text="Place an order and your points will show up here.">
                        <a href="{{ route('vendors.index') }}" class="btn btn-primary btn-sm">Browse vendors</a>
                    </x-empty-state>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection