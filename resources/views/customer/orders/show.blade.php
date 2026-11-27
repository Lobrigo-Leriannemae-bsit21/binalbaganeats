@extends('layouts.app')

@section('title', 'Order '.$order->order_number)

@section('content')
@php
    $payment = $order->payment;
    $rider = $order->delivery?->rider;
    $pointRows = $order->sukiPointTransactions;
@endphp

<div class="container py-4 py-lg-5">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">My orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Order {{ $order->order_number }}</h1>
            <p class="text-muted mb-0">
                {{ $order->vendorProfile?->business_name }} &middot; placed {{ $order->created_at->format('M j, Y g:i A') }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="be-badge {{ $order->order_status->badgeClass() }} fs-6">{{ $order->order_status->label() }}</span>
            @unless ($order->order_status->isFinal())
                <a href="{{ route('orders.track', $order) }}" class="btn btn-primary btn-sm">Track order</a>
            @endunless
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Items --}}
            <div class="be-card mb-4">
                <div class="p-3 border-bottom"><h2 class="h6 text-uppercase text-muted mb-0">Items</h2></div>
                @foreach ($order->items as $item)
                    <div class="d-flex justify-content-between align-items-center gap-3 p-3 {{ $loop->last ? '' : 'border-bottom' }}">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $item->menuItem->image_url }}" alt="" class="be-cart-img">
                            <div>
                                <div class="fw-semibold">{{ $item->menuItem->item_name }}</div>
                                <div class="small text-muted">{{ $item->quantity }} &times; ₱{{ number_format($item->unit_price, 2) }}</div>
                            </div>
                        </div>
                        <strong>₱{{ number_format($item->line_total, 2) }}</strong>
                    </div>
                @endforeach
            </div>

            {{-- Delivery --}}
            <div class="be-card p-4 mb-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Delivery</h2>
                <p class="mb-1"><i class="bi bi-geo-alt"></i> {{ $order->delivery_address }}</p>
                <p class="small text-muted mb-0">
                    Zone: {{ $order->deliveryZone?->zone_name }}
                    @if ($order->deliveryZone) &middot; about {{ $order->deliveryZone->estimated_travel_time }} min travel @endif
                </p>

                @if ($rider)
                    <hr>
                    <div class="d-flex align-items-center gap-3">
                        <span class="be-avatar">{{ $rider->initials }}</span>
                        <div>
                            <div class="fw-semibold">Rider: {{ $rider->name }}</div>
                            @if ($rider->phone)
                                <a href="tel:{{ $rider->phone }}" class="small"><i class="bi bi-telephone"></i> {{ $rider->phone }}</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- History --}}
            @if ($order->statusHistories->isNotEmpty())
                <div class="be-card p-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Status history</h2>
                    <ul class="list-unstyled mb-0">
                        @foreach ($order->statusHistories as $history)
                            <li class="d-flex justify-content-between gap-3 py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                                <span class="fw-semibold">{{ $history->status }}</span>
                                <span class="text-muted small">{{ $history->created_at->format('M j, Y g:i A') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-4">

            {{-- Totals --}}
            <div class="be-card p-4 mb-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Payment summary</h2>
                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>₱{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span>Delivery fee</span><span>₱{{ number_format($order->delivery_fee, 2) }}</span></div>
                @if ((float) $order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-success"><span>Suki Points discount</span><span>−₱{{ number_format($order->discount_amount, 2) }}</span></div>
                @endif
                <div class="d-flex justify-content-between fs-5 pt-2 border-top">
                    <strong>Total</strong><strong class="be-price">₱{{ number_format($order->total_amount, 2) }}</strong>
                </div>
            </div>

            {{-- Suki Points for this order --}}
            @if ($pointRows->isNotEmpty())
                <div class="be-card p-4 mb-4">
                    <h2 class="h6 text-uppercase text-muted mb-3"><i class="bi bi-gift"></i> Suki Points</h2>
                    @foreach ($pointRows as $row)
                        <div class="d-flex justify-content-between align-items-center gap-2 {{ $loop->last ? '' : 'mb-2' }}">
                            <span>{{ $row->type === \App\Enums\SukiPointType::Earned ? 'Earned' : 'Used' }}</span>
                            <span>
                                <strong class="{{ $row->signedPoints() >= 0 ? 'be-point-plus' : 'be-point-minus' }}">
                                    {{ $row->signedPoints() >= 0 ? '+' : '−' }}{{ number_format(abs($row->signedPoints())) }}
                                </strong>
                                <span class="be-badge {{ $row->badgeClass() }} ms-1">{{ $row->statusLabel() }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Payment --}}
            @if ($payment)
                <div class="be-card p-4 mb-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Payment</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Method</span><strong>{{ $payment->payment_method->label() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status</span>
                        <span class="be-badge {{ $payment->payment_status->badgeClass() }}">{{ $payment->payment_status->label() }}</span>
                    </div>

                    @if ($payment->transaction_reference)
                        <div class="d-flex justify-content-between mb-2 gap-2">
                            <span>Reference</span><strong class="text-break text-end">{{ $payment->transaction_reference }}</strong>
                        </div>
                    @endif

                    @if ($payment->proof_of_payment)
                        <a href="{{ route('orders.payment.proof', $order) }}" target="_blank" rel="noopener"
                           class="btn btn-outline-primary btn-sm w-100 mt-2">
                            <i class="bi bi-image"></i> View payment screenshot
                        </a>
                    @endif

                    @if ($payment->payment_status === \App\Enums\PaymentStatus::Rejected)
                        <div class="alert alert-danger small mt-3 mb-0">
                            Your payment could not be verified. Please check your details and submit them again.
                        </div>
                    @endif

                    @can('submitPayment', $order)
                        <form method="POST" action="{{ route('orders.payment.store', $order) }}" enctype="multipart/form-data" class="mt-3">
                            @csrf
                            <h3 class="h6">Submit payment details</h3>

                            <x-form.input name="transaction_reference" label="GCash reference number" :required="true"
                                          maxlength="40" autocomplete="off" />

                            <div class="mb-3">
                                <label for="payment_proof" class="form-label fw-semibold">Payment screenshot <span class="text-danger">*</span></label>
                                <input type="file" id="payment_proof" name="payment_proof" required
                                       accept="image/jpeg,image/png,image/webp"
                                       class="form-control @error('payment_proof') is-invalid @enderror">
                                @error('payment_proof')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Submit payment</button>
                        </form>
                    @endcan
                </div>
            @endif

            {{-- Cancel --}}
            @can('cancel', $order)
                <form method="POST" action="{{ route('orders.cancel', $order) }}"
                      data-confirm="Cancel this order? {{ $payment?->payment_method === \App\Enums\PaymentMethod::Gcash ? 'If you already paid via GCash, please contact the vendor about a refund. ' : '' }}Any Suki Points you used will be returned.">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-x-circle me-1"></i> Cancel order
                    </button>
                </form>
                <p class="small text-muted mt-2">You can cancel until the vendor accepts your order.</p>
            @endcan
        </div>
    </div>
</div>
@endsection