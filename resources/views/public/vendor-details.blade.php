@extends('layouts.app')

@section('title', $vendor['name'] . ' — BinalbaganEats')

@section('content')
<div class="container py-4">
    <a href="{{ route('public.vendors') }}" class="btn btn-sm btn-outline-be-primary mb-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Vendors
    </a>

    <div class="be-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-be-primary mb-0">{{ $vendor['name'] }}</h3>
                    <x-badge :status="$vendor['status']" />
                </div>
                <p class="text-be-muted mb-1">{{ $vendor['description'] }}</p>
                <small class="text-be-muted"><i class="bi bi-geo-alt text-be-accent me-1"></i>{{ $vendor['barangay'] }} &bull; Prep time: {{ $vendor['prep_time'] }}</small>
            </div>
            @guest
                <a href="{{ route('login') }}" class="btn btn-be-accent">Log in to Order</a>
            @else
                <a href="{{ route('customer.vendors.show', $vendor['id']) }}" class="btn btn-be-accent">Order in Customer Portal</a>
            @endguest
        </div>
    </div>

    <h5 class="fw-bold text-be-primary mb-3">Available Menu Items</h5>
    <div class="row g-3">
        @foreach ($vendor['menu'] as $item)
            <div class="col-md-6">
                <div class="be-card p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-be-primary mb-1">{{ $item['name'] }}</h6>
                        <small class="text-be-muted">Prep time: {{ $item['prep'] }}</small>
                        <div class="fw-bold text-be-accent mt-1">₱{{ number_format($item['price'], 2) }}</div>
                    </div>
                    <a href="{{ auth()->check() ? route('customer.vendors.show', $vendor['id']) : route('login') }}" class="btn btn-sm btn-be-primary">
                        Order Item
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection