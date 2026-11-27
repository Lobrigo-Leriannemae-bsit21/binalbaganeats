@extends('layouts.app')

@section('title', 'My orders')

@section('content')
<div class="container py-4 py-lg-5">

    <h1 class="h3 mb-4">My orders</h1>

    <div class="be-chip-row mb-4">
        @foreach (['all' => 'All', 'active' => 'Active', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled / Declined'] as $key => $label)
            <a href="{{ route('orders.index', $key === 'all' ? [] : ['status' => $key]) }}"
               class="be-cat-chip {{ $filter === $key ? 'active' : '' }}">
                {{ $label }} <span class="opacity-75">({{ $counts[$key] }})</span>
            </a>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        @if ($filter === 'all')
            <x-empty-state icon="bi-bag" title="No orders yet"
                           text="When you place an order, it will show up here.">
                <a href="{{ route('vendors.index') }}" class="btn btn-primary">Browse vendors</a>
            </x-empty-state>
        @else
            <x-empty-state icon="bi-funnel" title="No orders in this list"
                           text="Try another filter.">
                <a href="{{ route('orders.index') }}" class="btn btn-primary">Show all orders</a>
            </x-empty-state>
        @endif
    @else
        @foreach ($orders as $order)
            <div class="be-card p-3 p-md-4 mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <a href="{{ route('orders.show', $order) }}" class="fw-bold text-dark text-decoration-none">{{ $order->order_number }}</a>
                        <div class="small text-muted">
                            {{ $order->vendorProfile?->business_name ?? 'Vendor' }}
                            &middot; {{ $order->created_at->format('M j, Y g:i A') }}
                        </div>
                    </div>
                    <span class="be-badge {{ $order->order_status->badgeClass() }}">{{ $order->order_status->label() }}</span>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                    <div class="small text-muted">
                        {{ (int) $order->items_sum_quantity }} {{ \Illuminate\Support\Str::plural('item', (int) $order->items_sum_quantity) }}
                        &middot; {{ $order->payment?->payment_method->label() }}
                        @if ($order->payment)
                            <span class="be-badge {{ $order->payment->payment_status->badgeClass() }} ms-1">{{ $order->payment->payment_status->label() }}</span>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span class="be-price fs-5">₱{{ number_format($order->total_amount, 2) }}</span>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">Details</a>
                        @unless ($order->order_status->isFinal())
                            <a href="{{ route('orders.track', $order) }}" class="btn btn-primary btn-sm">Track</a>
                        @endunless
                    </div>
                </div>
            </div>
        @endforeach

        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection