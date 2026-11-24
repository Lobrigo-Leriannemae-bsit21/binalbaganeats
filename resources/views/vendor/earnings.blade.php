@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Earnings & Payouts')
@section('page-title', 'Earnings & Payment Settlement Summary')
@section('page-subtitle', 'Overview of direct GCash receipts and Cash on Delivery settlements')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="be-card p-4">
            <small class="text-be-muted fw-semibold">Direct GCash Verified Sales</small>
            <h2 class="fw-extrabold text-be-primary my-1">₱{{ number_format($gcashTotal, 2) }}</h2>
            <p class="small text-be-muted mb-0">Transferred directly to your registered GCash account by customers.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="be-card p-4 bg-be-sage border-0">
            <small class="text-be-primary fw-semibold">Cash on Delivery (COD) Sales</small>
            <h2 class="fw-extrabold text-be-accent my-1">₱{{ number_format($codTotal, 2) }}</h2>
            <p class="small text-be-muted mb-0">Collected and remitted by assigned BinalbaganEats delivery riders.</p>
        </div>
    </div>
</div>
@endsection