@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-4 py-lg-5">

    {{-- Greeting + search --}}
    <div class="be-card p-4 p-lg-5 mb-4">
        <h1 class="h3 mb-1">Good day, {{ \Illuminate\Support\Str::before($user->name, ' ') }}!</h1>
        <p class="text-muted mb-4">What are you craving today?</p>

        <form method="GET" action="{{ route('vendors.index') }}" role="search">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control" maxlength="100"
                       placeholder="Search food or vendors..." aria-label="Search food or vendors">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    {{-- Quick stats --}}
    <div class="row g-3 mb-5">
        <div class="col-4">
            <div class="be-card p-3 h-100">
                <p class="text-muted small text-uppercase mb-1">Active orders</p>
                <p class="h3 fw-bold mb-0">{{ $activeOrders }}</p>
            </div>
        </div>
        <div class="col-4">
            <div class="be-card p-3 h-100">
                <p class="text-muted small text-uppercase mb-1">Total orders</p>
                <p class="h3 fw-bold mb-0">{{ $totalOrders }}</p>
            </div>
        </div>
        <div class="col-4">
            <a href="{{ route('cart.index') }}" class="be-card p-3 h-100 d-block text-decoration-none text-dark">
                <p class="text-muted small text-uppercase mb-1">In my cart</p>
                <p class="h3 fw-bold mb-0">{{ $cartCount }}</p>
            </a>
        </div>
    </div>

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <h2 class="h5 mb-3">Categories</h2>
        <div class="row g-3 mb-5">
            @foreach ($categories as $category)
                <div class="col-4 col-sm-3 col-md-2">
                    <a href="{{ route('vendors.index', ['category' => $category]) }}" class="be-cat-tile">
                        <span class="be-step-icon"><i class="bi {{ \App\Models\MenuItem::categoryIcon($category) }}"></i></span>
                        <span>{{ $category }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Popular vendors --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Popular vendors</h2>
        <a href="{{ route('vendors.index') }}" class="small fw-semibold">View all</a>
    </div>

    @if ($popularVendors->isEmpty())
        <div class="mb-5">
            <x-empty-state icon="bi-shop" title="No vendors yet"
                           text="Local vendors will appear here as soon as they are approved." />
        </div>
    @else
        <div class="row g-4 mb-5">
            @foreach ($popularVendors as $vendor)
                <div class="col-sm-6 col-lg-4">
                    <x-vendor-card :vendor="$vendor" />
                </div>
            @endforeach
        </div>
    @endif

    {{-- New on the menu --}}
    @if ($newItems->isNotEmpty())
        <h2 class="h5 mb-3">New on the menu</h2>
        <div class="row g-4">
            @foreach ($newItems as $item)
                <div class="col-sm-6 col-lg-3">
                    <x-food-card :item="$item" :show-vendor="true" />
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection