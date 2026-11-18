@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Delivery Details')
@section('page-title', 'Delivery Details & Status Update')
@section('page-subtitle', 'Manage pickup and delivery milestones')

@section('content')
@if (!$selected)
    <div class="be-card p-5 text-center">
        <h5 class="text-be-primary">No active delivery selected.</h5>
    </div>
@else
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="be-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-be-primary mb-0">Order #{{ $selected['order_number'] }}</h5>
                    <x-badge :status="$selected['status']" />
                </div>
                <div class="mb-3">
                    <span class="fw-semibold">Vendor:</span> {{ $selected['vendor'] }}<br>
                    <span class="fw-semibold">Customer:</span> {{ $selected['customer'] }}<br>
                    <span class="fw-semibold">Delivery Address:</span> {{ $selected['address'] }} ({{ $selected['zone'] }})<br>
                    <span class="fw-semibold">Items:</span> {{ $selected['items'] }}
                </div>

                <hr>

                <form method="POST" action="{{ route('rider.status.update', $selected['order_number']) }}">
                    @csrf
                    <label class="form-label fw-semibold">Update Delivery Milestone Status</label>
                    <div class="input-group mb-3">
                        <select name="status" class="form-select">
                            <option value="Assigned" {{ $selected['status'] === 'Assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="Picked Up" {{ $selected['status'] === 'Picked Up' ? 'selected' : '' }}>Picked Up from Vendor</option>
                            <option value="Delivered" {{ $selected['status'] === 'Delivered' ? 'selected' : '' }}>Delivered to Customer</option>
                        </select>
                        <button type="submit" class="btn btn-be-accent">Save Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection