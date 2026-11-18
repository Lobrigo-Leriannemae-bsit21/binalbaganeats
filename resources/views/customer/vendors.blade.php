@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'Browse Vendors')
@section('page-title', 'Browse Local Vendors')
@section('page-subtitle', 'Approved participating vendors in Barangay San Pedro')

@section('content')
<div class="be-card p-3 mb-4">
    <form method="GET" action="{{ route('customer.vendors') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search vendor or dish...">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">All Food Categories</option>
                <option value="Carinderia & Rice Meals" {{ $category === 'Carinderia & Rice Meals' ? 'selected' : '' }}>Carinderia & Rice Meals</option>
                <option value="Noodles & Inasal" {{ $category === 'Noodles & Inasal' ? 'selected' : '' }}>Noodles & Inasal</option>
                <option value="Merienda & Desserts" {{ $category === 'Merienda & Desserts' ? 'selected' : '' }}>Merienda & Desserts</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-be-primary flex-grow-1">Filter</button>
            <a href="{{ route('customer.vendors') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="row g-4">
    @forelse ($vendors as $vendor)
        <div class="col-md-4">
            <div class="be-card h-100 p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-be-sage text-be-primary">{{ $vendor['category'] }}</span>
                    <x-badge :status="$vendor['status']" />
                </div>
                <h5 class="fw-bold text-be-primary">{{ $vendor['name'] }}</h5>
                <p class="small text-be-muted flex-grow-1">{{ $vendor['description'] }}</p>
                <div class="small text-be-muted border-top pt-2 mb-3 d-flex justify-content-between">
                    <span><i class="bi bi-clock text-be-accent me-1"></i> {{ $vendor['prep_time'] }} mins prep</span>
                    <span><i class="bi bi-star-fill text-warning me-1"></i> {{ $vendor['rating'] }}</span>
                </div>
                <a href="{{ route('customer.vendors.show', $vendor['id']) }}" class="btn btn-be-accent w-100">
                    Open Storefront & Menu
                </a>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="be-card p-5 text-center">
                <h5 class="text-be-primary">No participating vendors matched your search.</h5>
            </div>
        </div>
    @endforelse
</div>
@endsection