@extends('layouts.app')

@section('title', 'BinalbaganEats — Local food. On the way.')

@section('content')
<div class="container py-4">
    {{-- Hero Banner --}}
    <div class="be-card p-4 p-lg-5 mb-5 bg-white">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-be-sage text-be-primary px-3 py-2 mb-3">
                    <i class="bi bi-geo-alt-fill text-be-accent me-1"></i> Serving Barangay San Pedro & Binalbagan
                </span>
                <h1 class="display-5 fw-extrabold text-be-primary mb-3">
                    Local food. <span class="text-be-accent">On the way.</span>
                </h1>
                <p class="lead text-be-muted mb-4">
                    Order authentic meals from your favorite carinderias and food stalls in Barangay San Pedro, earn Suki Points, or request a rider through our Buy-for-Me errand service.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('public.vendors') }}" class="btn btn-be-accent btn-lg px-4">
                        <i class="bi bi-shop me-2"></i> Browse Vendors
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-be-primary btn-lg px-4">
                        Partner With Us
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="bg-be-cream rounded-4 p-4 border">
                    <div class="rounded-circle bg-be-sage d-inline-flex align-items-center justify-content-center mb-3 overflow-hidden" style="width: 150px; height: 150px;">
                        <img src="{{ asset('images/binalbaganeats-scooter-mark.png') }}" alt="BinalbaganEats delivery scooter logo" class="be-logo-card">
                    </div>
                    <h4 class="fw-bold text-be-primary mb-1">Binalbaganeats</h4>
                    <p class="text-be-muted small mb-0">Fast zone-based delivery & Suki loyalty rewards</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Featured Participating Vendors --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-be-primary mb-0">Featured Vendors in Barangay San Pedro</h4>
            <small class="text-be-muted">Approved participating establishments ready for your order</small>
        </div>
        <a href="{{ route('public.vendors') }}" class="btn btn-sm btn-outline-be-primary">View All</a>
    </div>

    <div class="row g-4">
        @foreach ($vendors as $vendor)
            <div class="col-md-4">
                <div class="be-card h-100 p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-be-sage text-be-primary">{{ $vendor['category'] }}</span>
                        <x-badge :status="$vendor['status']" />
                    </div>
                    <h5 class="fw-bold text-be-primary mt-2">{{ $vendor['name'] }}</h5>
                    <p class="small text-be-muted flex-grow-1">{{ $vendor['description'] }}</p>
                    <div class="d-flex justify-content-between align-items-center small text-be-muted border-top pt-3 mt-2">
                        <span><i class="bi bi-clock me-1 text-be-accent"></i> {{ $vendor['prep_time'] }}</span>
                        <span><i class="bi bi-star-fill text-warning me-1"></i> {{ $vendor['rating'] }}</span>
                    </div>
                    <a href="{{ route('public.vendors.show', $vendor['id']) }}" class="btn btn-be-primary w-100 mt-3">
                        View Menu
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection