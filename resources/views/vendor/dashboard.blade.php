@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Vendor Dashboard')
@section('page-title', $storefront['name'])
@section('page-subtitle', 'Local Food Vendor Portal • ' . $storefront['barangay'])

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Incoming Orders</small>
            <h3 class="fw-bold text-be-accent mb-0 mt-1">{{ $incomingCount }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">In Preparation</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $activePrepCount }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Completed Orders</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $completedCount }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Recorded Food Sales</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">₱{{ number_format($totalSales, 2) }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="be-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-be-primary mb-0">Recent Customer Orders</h6>
                <a href="{{ route('vendor.incoming-orders') }}" class="btn btn-sm btn-be-accent">Manage Incoming Orders</a>
            </div>
            <div class="table-responsive">
                <table class="table be-table mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-be-primary">{{ $order['order_number'] }}</td>
                                <td>{{ $order['customer'] }}</td>
                                <td>
                                    <div class="small">{{ $order['payment_method'] }}</div>
                                    <x-badge :status="$order['payment_status']" />
                                </td>
                                <td class="fw-bold">₱{{ number_format($order['total'], 2) }}</td>
                                <td><x-badge :status="$order['status']" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="be-card p-4 bg-be-sage border-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold text-be-primary mb-0">Storefront Status</h6>
                <x-badge :status="$storefront['status']" />
            </div>
            <p class="small text-be-muted mb-2">Est. Prep Time: <strong>{{ $storefront['prep_time'] }} mins</strong> (feeds customer ETA calculations)</p>
            <p class="small text-be-muted mb-3">Hours: {{ $storefront['operating_hours'] }}</p>
            <div class="d-grid gap-2">
                <a href="{{ route('vendor.storefront') }}" class="btn btn-be-primary btn-sm">Update Storefront & Prep Time</a>
                <a href="{{ route('vendor.menu') }}" class="btn btn-outline-be-primary btn-sm">Manage Menu Items</a>
            </div>
        </div>
    </div>
</div>
@endsection