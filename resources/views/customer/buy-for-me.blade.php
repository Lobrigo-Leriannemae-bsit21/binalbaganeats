@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'Buy-for-Me Errand Service')
@section('page-title', 'Buy-for-Me Service')
@section('page-subtitle', 'Request items from establishments in Binalbagan not yet registered as participating vendors')

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="be-card p-4">
            <h5 class="fw-bold text-be-primary mb-3">New Buy-for-Me Request</h5>
            <form method="POST" action="{{ route('customer.buy-for-me.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unregistered Establishment / Store Name</label>
                    <input type="text" name="establishment" class="form-control" placeholder="e.g., Binalbagan Public Market Bakery" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Requested Items & Quantities</label>
                    <textarea name="items" rows="3" class="form-control" placeholder="List exact items, quantities, and notes..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Maximum Item Budget (₱)</label>
                    <input type="number" name="budget" min="50" max="5000" step="0.01" class="form-control" placeholder="e.g., 300.00" required>
                    <div class="form-text">Riders use personal funds initially and will not exceed your stated maximum budget.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Delivery Zone</label>
                    <select name="zone" class="form-select" required>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone['name'] }}">{{ $zone['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Delivery Address</label>
                    <input type="text" name="delivery_address" value="{{ auth()->user()->address }}" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-be-accent w-100">Submit Buy-for-Me Request</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="be-card p-4">
            <h5 class="fw-bold text-be-primary mb-3">My Buy-for-Me Requests</h5>
            @foreach ($requests as $req)
                <div class="border rounded-3 p-3 mb-3 bg-be-cream">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="fw-bold text-be-primary">{{ $req['request_id'] }}</span> — <strong>{{ $req['establishment'] }}</strong>
                        </div>
                        <x-badge :status="$req['status']" />
                    </div>
                    <p class="small mb-2">{{ $req['items'] }}</p>
                    <div class="row small text-be-muted g-2">
                        <div class="col-sm-6">Max Budget: <strong>₱{{ number_format($req['budget'], 2) }}</strong></div>
                        <div class="col-sm-6">Actual Expense: <strong>{{ $req['actual_expense'] ? '₱' . number_format($req['actual_expense'], 2) : 'Pending Purchase' }}</strong></div>
                        <div class="col-sm-6">Rider: {{ $req['rider'] }}</div>
                        <div class="col-sm-6">Settlement: {{ $req['settlement_status'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection