@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'Customer Dashboard')
@section('page-title', 'Customer Dashboard')
@section('page-subtitle', 'Welcome back, ' . auth()->user()->name)

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Active Orders</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $activeCount }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Suki Points Balance</small>
            <h3 class="fw-bold text-be-accent mb-0 mt-1">{{ $sukiPoints }} pts</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Cart Items</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $cartCount }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Total Orders Placed</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $totalOrders }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="be-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-be-primary mb-0">Recent Orders</h6>
                <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-be-primary">View All Orders</a>
            </div>
            <div class="table-responsive">
                <table class="table be-table mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Vendor</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td class="fw-bold text-be-primary">{{ $order['order_number'] }}</td>
                                <td>{{ $order['vendor'] }}</td>
                                <td class="fw-semibold">₱{{ number_format($order['total'], 2) }}</td>
                                <td><x-badge :status="$order['status']" /></td>
                                <td>
                                    <a href="{{ route('customer.tracking', $order['order_number']) }}" class="btn btn-sm btn-be-primary">Track</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="be-card p-4 mb-4 bg-be-sage border-0">
            <h6 class="fw-bold text-be-primary"><i class="bi bi-bag-heart-fill text-be-accent me-1"></i> Need Something Outside Registered Vendors?</h6>
            <p class="small text-be-muted mb-3">Use our Buy-for-Me service to request items from unregistered local stores in Binalbagan within your budget.</p>
            <a href="{{ route('customer.buy-for-me') }}" class="btn btn-be-accent w-100">Create Buy-for-Me Request</a>
        </div>

        <div class="be-card p-4">
            <h6 class="fw-bold text-be-primary mb-2">Quick Actions</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('customer.vendors') }}" class="btn btn-outline-be-primary text-start"><i class="bi bi-shop me-2"></i> Browse San Pedro Vendors</a>
                <a href="{{ route('customer.suki-points') }}" class="btn btn-outline-be-primary text-start"><i class="bi bi-award me-2"></i> Redeem Suki Points</a>
            </div>
        </div>
    </div>
</div>
@endsection