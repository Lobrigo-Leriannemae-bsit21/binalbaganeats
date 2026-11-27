@extends('layouts.app')

@section('title', 'Order placed')

@section('content')
@php
    $payment = $order->payment;
    $eta = ($order->vendorProfile?->estimated_prep_time ?? 0) + ($order->deliveryZone?->estimated_travel_time ?? 0);
    $pointRows = $order->sukiPointTransactions;
    $earned = $pointRows->first(fn ($row) => $row->type === \App\Enums\SukiPointType::Earned);
    $redeemed = $pointRows->first(fn ($row) => $row->type === \App\Enums\SukiPointType::Redeemed);
@endphp

<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="be-card p-4 p-md-5 text-center">
                <div class="be-success-check mx-auto mb-3"><i class="bi bi-check-lg"></i></div>
                <h1 class="h3 mb-1">Order Placed!</h1>
                <p class="text-muted mb-4">Your order has been submitted to {{ $order->vendorProfile?->business_name ?? 'the vendor' }}.</p>

                <p class="mb-1 text-muted small text-uppercase">Order number</p>
                <p class="h4 fw-bold mb-4">{{ $order->order_number }}</p>

                <div class="text-start be-inset p-3 mb-4">
                    @if ($redeemed)
                        <div class="d-flex justify-content-between py-1 text-success">
                            <span>Suki Points discount ({{ number_format($redeemed->points_amount) }} pts)</span>
                            <strong>−₱{{ number_format($order->discount_amount, 2) }}</strong>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between py-1"><span>Total</span><strong class="be-price">₱{{ number_format($order->total_amount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between py-1"><span>Payment</span><strong>{{ $payment?->payment_method->label() }}</strong></div>
                    <div class="d-flex justify-content-between py-1">
                        <span>Payment status</span>
                        @if ($payment)<span class="be-badge {{ $payment->payment_status->badgeClass() }}">{{ $payment->payment_status->label() }}</span>@endif
                    </div>
                    <div class="d-flex justify-content-between py-1"><span>Estimated time</span><strong>about {{ $eta }} min</strong></div>
                    <div class="d-flex justify-content-between py-1 gap-3"><span>Deliver to</span><strong class="text-end">{{ $order->delivery_address }}</strong></div>
                </div>

                @if ($earned)
                    <p class="small text-success mb-3">
                        <i class="bi bi-gift"></i> You’ll earn <strong>{{ number_format($earned->points_amount) }} Suki {{ \Illuminate\Support\Str::plural('Point', $earned->points_amount) }}</strong> once this order is delivered.
                    </p>
                @endif

                @if ($payment?->payment_method === \App\Enums\PaymentMethod::Cod)
                    <p class="small text-muted">Please prepare <strong>₱{{ number_format($order->total_amount, 2) }}</strong> in cash for the rider.</p>
                @else
                    <p class="small text-muted">Your GCash details were sent. The vendor will verify your payment.</p>
                @endif

                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ route('orders.track', $order) }}" class="btn btn-primary">Track my order</a>
                    <a href="{{ route('vendors.index') }}" class="btn btn-outline-primary">Continue shopping</a>
                </div>
                <a href="{{ route('orders.show', $order) }}" class="d-inline-block mt-3 small">View order details</a>
            </div>
        </div>
    </div>
</div>
@endsection