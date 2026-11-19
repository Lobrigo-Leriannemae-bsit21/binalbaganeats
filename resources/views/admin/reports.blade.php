@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Reports & Analytics')
@section('page-title', 'Basic Operational Summaries & Reports')
@section('page-subtitle', 'Platform-wide operational summary across Binalbagan zones')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Recorded Transaction Volume</small>
            <h3 class="fw-bold text-be-accent mb-0 mt-1">₱{{ number_format($totalVolume, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Total Tracked Orders & Errands</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $orderCount }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Active Platform Users</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $customerCount + $vendorCount + $riderCount }}</h3>
        </div>
    </div>
</div>

<div class="be-card p-4">
    <h6 class="fw-bold text-be-primary mb-3">Platform Role Distribution</h6>
    <ul class="list-group list-group-flush">
        <li class="list-group-item px-0 d-flex justify-content-between">
            <span>Registered Customers</span>
            <strong>{{ $customerCount }}</strong>
        </li>
        <li class="list-group-item px-0 d-flex justify-content-between">
            <span>Approved Participating Vendors (Brgy. San Pedro)</span>
            <strong>{{ $vendorCount }}</strong>
        </li>
        <li class="list-group-item px-0 d-flex justify-content-between">
            <span>Approved Delivery Riders</span>
            <strong>{{ $riderCount }}</strong>
        </li>
    </ul>
</div>
@endsection