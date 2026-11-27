@extends('layouts.app')

@section('title', $vendor->business_name)

@section('content')
<div class="container py-4 py-lg-5">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vendors.index') }}">Vendors</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $vendor->business_name }}</li>
        </ol>
    </nav>

    {{-- Store header --}}
    <div class="be-card overflow-hidden mb-4">
        <div class="be-banner">
            <img src="{{ $vendor->image_url }}" alt="{{ $vendor->business_name }}">
        </div>
        <div class="p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                    <h1 class="h3 mb-1">{{ $vendor->business_name }}</h1>
                    <p class="text-muted mb-0"><i class="bi bi-geo-alt"></i> {{ $vendor->business_address }}</p>
                </div>
                <span class="be-badge {{ $vendor->is_available ? 'be-badge-success' : 'be-badge-danger' }}">
                    {{ $vendor->is_available ? 'Open · Delivery available' : 'Closed' }}
                </span>
            </div>
            <p class="small text-muted mt-3 mb-0">
                <i class="bi bi-clock"></i> Usually ready in about {{ $vendor->estimated_prep_time }} minutes
            </p>
        </div>
    </div>

    @unless ($vendor->is_available)
        <div class="alert alert-warning">
            This store is currently closed. You can browse the menu, but ordering is paused.
        </div>
    @endunless

    {{-- Tabs --}}
    <ul class="nav nav-tabs be-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="menu-tab" data-bs-toggle="tab" data-bs-target="#tab-menu"
                    type="button" role="tab" aria-controls="tab-menu" aria-selected="true">Menu</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#tab-about"
                    type="button" role="tab" aria-controls="tab-about" aria-selected="false">About</button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- Menu --}}
        <div class="tab-pane fade show active" id="tab-menu" role="tabpanel" aria-labelledby="menu-tab" tabindex="0">

            <form method="GET" action="{{ route('vendors.show', $vendor) }}" role="search" class="mb-3">
                @if ($category)
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" name="q" value="{{ $q }}" maxlength="100" class="form-control"
                           placeholder="Search this menu..." aria-label="Search this menu">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

            @if ($storeCategories->isNotEmpty())
                <div class="be-chip-row mb-3">
                    <a href="{{ route('vendors.show', array_merge(['vendor' => $vendor], array_filter(['q' => $q]))) }}"
                       class="be-cat-chip {{ $category === null ? 'active' : '' }}">All</a>
                    @foreach ($storeCategories as $name)
                        <a href="{{ route('vendors.show', array_merge(['vendor' => $vendor], array_filter(['q' => $q, 'category' => $name]))) }}"
                           class="be-cat-chip {{ $category === $name ? 'active' : '' }}">{{ $name }}</a>
                    @endforeach
                </div>
            @endif

            @forelse ($grouped as $name => $items)
                <h2 class="h5 mt-4 mb-3">{{ $name }}</h2>
                <div class="row g-3">
                    @foreach ($items as $item)
                        <div class="col-sm-6 col-lg-4">
                            <x-food-card :item="$item" />
                        </div>
                    @endforeach
                </div>
            @empty
                @if ($q || $category)
                    <x-empty-state icon="bi-search" title="No dishes match your search"
                                   text="Try a different word or category.">
                        <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-primary">Show full menu</a>
                    </x-empty-state>
                @else
                    <x-empty-state icon="bi-egg-fried" title="No menu items yet"
                                   text="This vendor has not added any dishes." />
                @endif
            @endforelse
        </div>

        {{-- About --}}
        <div class="tab-pane fade" id="tab-about" role="tabpanel" aria-labelledby="about-tab" tabindex="0">
            <div class="be-card p-4">
                <h2 class="h5 mb-3">About {{ $vendor->business_name }}</h2>
                <p>{{ $vendor->description ?: 'This vendor has not added a description yet.' }}</p>

                <ul class="list-unstyled mb-0">
                    <li class="py-2 border-top"><i class="bi bi-geo-alt me-2"></i>{{ $vendor->business_address }}</li>
                    <li class="py-2 border-top"><i class="bi bi-clock me-2"></i>About {{ $vendor->estimated_prep_time }} minutes to prepare an order</li>
                    <li class="py-2 border-top">
                        <i class="bi bi-wallet2 me-2"></i>Accepts:
                        Cash on Delivery{{ $vendor->gcash_number ? ' and GCash' : '' }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection