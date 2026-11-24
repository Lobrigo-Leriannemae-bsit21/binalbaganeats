@extends('layouts.portal', ['role' => 'customer'])

@section('title', $vendor['name'])
@section('page-title', $vendor['name'])
@section('page-subtitle', $vendor['barangay'] . ' • ' . $vendor['category'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('customer.vendors') }}" class="btn btn-sm btn-outline-be-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Vendors
    </a>
    <a href="{{ route('customer.cart') }}" class="btn btn-sm btn-be-accent">
        <i class="bi bi-cart3 me-1"></i> View Cart ({{ array_sum(array_column(session('customer_cart', []), 'qty')) }})
    </a>
</div>

<div class="be-card p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="fw-bold text-be-primary mb-1">{{ $vendor['name'] }}</h4>
            <p class="text-be-muted mb-0">{{ $vendor['description'] }}</p>
        </div>
        <div class="text-md-end">
            <x-badge :status="$vendor['status']" />
            <div class="small text-be-muted mt-1">Est. Prep Time: <strong>{{ $vendor['prep_time'] }} mins</strong></div>
        </div>
    </div>
</div>

<div class="row g-3">
    @foreach ($vendor['menu'] as $item)
        <div class="col-md-6">
            <div class="be-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="fw-bold text-be-primary mb-1">{{ $item['name'] }}</h6>
                        <x-badge :status="$item['available'] ? 'Available' : 'Unavailable'" />
                    </div>
                    <p class="small text-be-muted mb-2">{{ $item['desc'] }}</p>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fs-5 fw-bold text-be-accent">₱{{ number_format($item['price'], 2) }}</span>
                        <small class="text-be-muted"><i class="bi bi-clock me-1"></i>{{ $item['prep'] }} mins</small>
                    </div>
                </div>

                @if ($item['available'])
                    <form method="POST" action="{{ route('customer.cart.add') }}" class="d-flex gap-2">
                        @csrf
                        <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                        <input type="hidden" name="vendor_name" value="{{ $vendor['name'] }}">
                        <input type="hidden" name="item_id" value="{{ $item['id'] }}">
                        <input type="hidden" name="item_name" value="{{ $item['name'] }}">
                        <input type="hidden" name="price" value="{{ $item['price'] }}">
                        <input type="hidden" name="prep" value="{{ $item['prep'] }}">
                        <input type="number" name="qty" value="1" min="1" max="20" class="form-control" style="width: 80px;">
                        <button type="submit" class="btn btn-be-primary flex-grow-1">
                            <i class="bi bi-cart-plus me-1"></i> Add to Cart
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary w-100" disabled>Item Currently Unavailable</button>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection