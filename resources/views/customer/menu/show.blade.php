@extends('layouts.app')

@section('title', $item->item_name)

@section('content')
<div class="container py-4 py-lg-5">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendors</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendors.show', $vendor) }}">{{ $vendor->business_name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $item->item_name }}</li>
        </ol>
    </nav>

    {{-- Shown when the customer tries to mix vendors in one cart --}}
    @if (session('cart_conflict'))
        <div class="alert alert-warning">
            <p class="mb-2">
                <strong>Your cart has items from another vendor.</strong>
                You can order from one vendor at a time. Start a new cart with this item?
                Your current cart will be emptied.
            </p>
            <form method="POST" action="{{ route('cart.store') }}" class="d-flex flex-wrap gap-2">
                @csrf
                <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                <input type="hidden" name="quantity" value="{{ session('cart_conflict.quantity', 1) }}">
                <input type="hidden" name="replace" value="1">
                <button type="submit" class="btn btn-primary btn-sm">Start a new cart</button>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-sm">Keep my current cart</a>
            </form>
        </div>
    @endif

    <div class="row g-4 g-lg-5 mb-5">
        <div class="col-md-6">
            <img src="{{ $item->image_url }}" alt="{{ $item->item_name }}" class="be-detail-img {{ $item->is_available ? '' : 'is-unavailable' }}">
        </div>

        <div class="col-md-6">
            <span class="be-badge be-badge-neutral mb-2">{{ $item->category }}</span>
            <h1 class="h2 mb-1">{{ $item->item_name }}</h1>
            <p class="mb-3">
                from <a href="{{ route('vendors.show', $vendor) }}" class="fw-semibold">{{ $vendor->business_name }}</a>
            </p>

            <p class="display-6 be-price mb-3">₱{{ number_format($item->price, 2) }}</p>

            @if ($item->description)
                <p class="mb-3">{{ $item->description }}</p>
            @endif

            <p class="small text-muted mb-4">
                <i class="bi bi-clock"></i> Preparation time: about {{ $item->prep_time }} minutes
            </p>

            @if ($orderable)
                <form method="POST" action="{{ route('cart.store') }}" class="d-flex flex-wrap gap-3 align-items-center">
                    @csrf
                    <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                    <x-quantity-input :max="$maxQuantity" />
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-cart-plus me-1"></i> Add to cart
                    </button>
                </form>
            @else
                <div class="alert alert-secondary mb-0">
                    {{ $item->is_available ? 'This store is currently closed.' : 'This item is currently unavailable.' }}
                </div>
            @endif
        </div>
    </div>

    @if ($related->isNotEmpty())
        <h2 class="h5 mb-3">More from {{ $vendor->business_name }}</h2>
        <div class="row g-3">
            @foreach ($related as $other)
                <div class="col-sm-6 col-lg-3">
                    <x-food-card :item="$other" />
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection