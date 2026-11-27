@extends('layouts.app')

@section('title', 'Track '.$order->order_number)

@section('content')
@php
    $eta = ($order->vendorProfile?->estimated_prep_time ?? 0) + ($order->deliveryZone?->estimated_travel_time ?? 0);
    $rider = $order->delivery?->rider;
@endphp

<div class="container py-4 py-lg-5">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">My orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Track</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Track your order</h1>
        <span class="be-badge {{ $order->order_status->badgeClass() }} fs-6">{{ $order->order_status->label() }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="be-card p-4">
                <x-order-stepper :status="$order->order_status" />

                @unless ($order->order_status->isFinal())
                    <p class="small text-muted mt-4 mb-0">
                        <i class="bi bi-arrow-repeat"></i> This page refreshes automatically.
                        <a href="{{ route('orders.track', $order) }}">Refresh now</a>
                    </p>
                @endunless
            </div>

            @if ($order->statusHistories->isNotEmpty())
                <div class="be-card p-4 mt-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Activity</h2>
                    <ul class="list-unstyled mb-0">
                        @foreach ($order->statusHistories->reverse() as $history)
                            <li class="d-flex justify-content-between gap-3 py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                                <span class="fw-semibold">{{ $history->status }}</span>
                                <span class="text-muted small">{{ $history->created_at->format('M j, g:i A') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="be-card p-4 mb-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Order {{ $order->order_number }}</h2>
                <p class="mb-1"><strong>{{ $order->vendorProfile?->business_name }}</strong></p>
                @foreach ($order->items as $item)
                    <div class="d-flex justify-content-between small py-1">
                        <span>{{ $item->quantity }} &times; {{ $item->menuItem->item_name }}</span>
                        <span>₱{{ number_format($item->line_total, 2) }}</span>
                    </div>
                @endforeach
                <div class="d-flex justify-content-between pt-2 mt-2 border-top">
                    <strong>Total</strong><strong class="be-price">₱{{ number_format($order->total_amount, 2) }}</strong>
                </div>
                <p class="small text-muted mt-3 mb-0"><i class="bi bi-geo-alt"></i> {{ $order->delivery_address }}</p>
                @unless ($order->order_status->isFinal())
                    <p class="small text-muted mb-0"><i class="bi bi-clock"></i> Estimated: about {{ $eta }} min</p>
                @endunless
            </div>

            @if ($rider)
                <div class="be-card p-4 mb-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Your rider</h2>
                    <div class="d-flex align-items-center gap-3">
                        <span class="be-avatar">{{ $rider->initials }}</span>
                        <div>
                            <div class="fw-semibold">{{ $rider->name }}</div>
                            @if ($rider->phone)
                                <a href="tel:{{ $rider->phone }}" class="small"><i class="bi bi-telephone"></i> {{ $rider->phone }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary w-100">View order details</a>
        </div>
    </div>
</div>
@endsection

@unless ($order->order_status->isFinal())
    @push('scripts')
        <script>
            // Reload every 30 seconds, but only while the tab is visible.
            setTimeout(function reload() {
                if (document.hidden) { setTimeout(reload, 5000); return; }
                window.location.reload();
            }, 30000);
        </script>
    @endpush
@endunless