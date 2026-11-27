@extends('layouts.vendor')

@section('title', 'Dashboard')

@section('content')
@php
    $peso = fn (int $cents) => '₱'.number_format($cents / 100, 2);
    $chartTotal = array_sum(array_column($chart, 'cents'));

    $stats = [
        ['Total orders', number_format($summary['total_orders']), 'bi-receipt', 'All orders received'],
        ['Total sales', $peso($summary['sales_cents']), 'bi-cash-stack', $summary['delivered_orders'].' delivered '.\Illuminate\Support\Str::plural('order', $summary['delivered_orders'])],
        ['Pending orders', number_format($summary['pending_orders']), 'bi-hourglass-split', 'Waiting for your response'],
        ['Today’s orders', number_format($summary['today_orders']), 'bi-calendar-check', 'Placed today'],
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Good day, {{ $store->business_name }}!</h1>
        <p class="text-muted mb-0">Here’s your store overview.</p>
    </div>

    <form method="POST" action="{{ route('vendor.store.availability') }}"
          @if ($store->is_available) data-confirm="Close your store? Customers will not be able to place new orders." @endif>
        @csrf
        @method('PATCH')
        <input type="hidden" name="is_available" value="{{ $store->is_available ? 0 : 1 }}">
        <button type="submit" class="btn {{ $store->is_available ? 'btn-outline-danger' : 'btn-primary' }}">
            <i class="bi {{ $store->is_available ? 'bi-door-closed' : 'bi-door-open' }} me-1"></i>
            {{ $store->is_available ? 'Close store' : 'Open store' }}
        </button>
    </form>
</div>

@unless ($store->is_available)
    <div class="alert alert-warning">Your store is <strong>closed</strong>. Customers can browse your menu but can’t place orders.</div>
@endunless

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    @foreach ($stats as [$label, $value, $icon, $hint])
        <div class="col-6 col-xl-3">
            <div class="be-card be-stat h-100">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <p class="text-muted small text-uppercase mb-1">{{ $label }}</p>
                        <p class="be-stat-value mb-1">{{ $value }}</p>
                        <p class="small text-muted mb-0">{{ $hint }}</p>
                    </div>
                    <span class="be-stat-icon"><i class="bi {{ $icon }}"></i></span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    {{-- Sales chart --}}
    <div class="col-lg-8">
        <div class="be-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h5 mb-0">Sales overview</h2>
                    <p class="small text-muted mb-0">Food sales from delivered orders, last 7 days</p>
                </div>
                <span class="fw-bold be-price">{{ $peso($chartTotal) }}</span>
            </div>

            @if ($chartTotal === 0)
                <x-empty-state icon="bi-bar-chart" title="No sales yet"
                               text="Delivered orders from the last 7 days will show here." />
            @else
                <div class="be-bars" role="img" aria-label="Food sales for each of the last 7 days">
                    @foreach ($chart as $day)
                        <div class="be-bar" title="{{ $day['full'] }}: {{ $peso($day['cents']) }} from {{ $day['orders'] }} {{ \Illuminate\Support\Str::plural('order', $day['orders']) }}">
                            <span class="be-bar-value">{{ $day['cents'] > 0 ? '₱'.number_format($day['cents'] / 100) : '' }}</span>
                            <div class="be-bar-track"><div class="be-bar-fill" style="height: {{ $day['percent'] }}%"></div></div>
                            <span class="be-bar-label">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Store snapshot --}}
    <div class="col-lg-4">
        <div class="be-card p-4 h-100">
            <h2 class="h5 mb-3">Your store</h2>
            <img src="{{ $store->image_url }}" alt="{{ $store->business_name }}" class="be-store-thumb mb-3">
            <ul class="list-unstyled small mb-3">
                <li class="d-flex justify-content-between py-1"><span class="text-muted">Menu items</span><strong>{{ $summary['menu_total'] }}</strong></li>
                <li class="d-flex justify-content-between py-1"><span class="text-muted">Available now</span><strong>{{ $summary['menu_available'] }}</strong></li>
                <li class="d-flex justify-content-between py-1"><span class="text-muted">Prep time</span><strong>~{{ $store->estimated_prep_time }} min</strong></li>
                <li class="d-flex justify-content-between py-1">
                    <span class="text-muted">GCash</span>
                    <strong>{{ $store->gcash_number ? 'On' : 'Off' }}</strong>
                </li>
            </ul>
            <a href="{{ route('vendor.store.edit') }}" class="btn btn-outline-primary w-100">Manage store</a>
        </div>
    </div>
</div>

{{-- Recent orders --}}
<div class="be-card">
    <div class="p-3 border-bottom"><h2 class="h5 mb-0">Recent orders</h2></div>

    @if ($recentOrders->isEmpty())
        <div class="p-4">
            <x-empty-state icon="bi-bag" title="No orders yet" text="New orders from customers will appear here." />
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Order</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Items</th>
                        <th scope="col" class="text-end">Food total</th>
                        <th scope="col">Status</th>
                        <th scope="col">Placed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr>
                            <td class="fw-semibold">{{ $order->order_number }}</td>
                            <td>{{ \Illuminate\Support\Str::before($order->customer?->name ?? 'Customer', ' ') }}</td>
                            <td>{{ (int) $order->items_sum_quantity }}</td>
                            <td class="text-end">₱{{ number_format($order->subtotal, 2) }}</td>
                            <td><span class="be-badge {{ $order->order_status->badgeClass() }}">{{ $order->order_status->label() }}</span></td>
                            <td class="text-nowrap text-muted small">{{ $order->created_at->format('M j, g:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection