@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Incoming Orders')
@section('page-title', 'Incoming Orders & GCash Verification')
@section('page-subtitle', 'Verify external GCash references and accept or decline new orders')

@section('content')
@forelse ($orders as $order)
    <div class="be-card p-4 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold text-be-primary mb-0">{{ $order['order_number'] }}</h5>
                    <x-badge :status="$order['status']" />
                    @if ($order['flagged']) <x-badge status="Flagged" /> @endif
                </div>
                <small class="text-be-muted">Customer: <strong>{{ $order['customer'] }}</strong> &bull; {{ $order['zone'] }} &bull; {{ $order['created_at'] }}</small>
            </div>
            <div class="text-md-end">
                <div class="fs-5 fw-bold text-be-accent">₱{{ number_format($order['total'], 2) }}</div>
                <small class="text-be-muted">Food Subtotal: ₱{{ number_format($order['subtotal'], 2) }}</small>
            </div>
        </div>

        <div class="bg-be-cream p-3 rounded-3 mb-3">
            <div class="fw-semibold text-be-primary mb-1">Ordered Items:</div>
            <div>{{ $order['items'] }}</div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="fw-semibold">Payment:</span> {{ $order['payment_method'] }}
                <x-badge :status="$order['payment_status']" />
                @if ($order['gcash_ref'])
                    <span class="ms-2 badge bg-white text-be-primary border">Ref #: {{ $order['gcash_ref'] }}</span>
                @endif
            </div>

            <div class="d-flex flex-wrap gap-2">
                {{-- UC-09.1 Manual GCash Verification --}}
                @if ($order['payment_method'] === 'GCash' && $order['payment_status'] !== 'Verified')
                    <form method="POST" action="{{ route('vendor.orders.verify-gcash', $order['order_number']) }}">
                        @csrf
                        <input type="hidden" name="verification_action" value="verify">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-patch-check me-1"></i> Verify GCash Payment
                        </button>
                    </form>
                @endif

                {{-- UC-09 Accept / Decline / Flag --}}
                <form method="POST" action="{{ route('vendor.orders.decision', $order['order_number']) }}" class="d-flex gap-2">
                    @csrf
                    <button type="submit" name="decision" value="accept" class="btn btn-sm btn-be-primary">Accept Order</button>
                    <button type="submit" name="decision" value="decline" class="btn btn-sm btn-outline-danger">Decline</button>
                    <button type="submit" name="decision" value="flag" class="btn btn-sm btn-outline-warning">Flag</button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="be-card p-5 text-center">
        <h5 class="text-be-primary">No pending incoming orders right now.</h5>
    </div>
@endforelse
@endsection