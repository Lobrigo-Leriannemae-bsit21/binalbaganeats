@extends('layouts.app')

@section('title', 'Browse vendors')

@section('content')
<div class="container py-4 py-lg-5">

    <div class="mb-4">
        <h1 class="h3 mb-1">Browse vendors</h1>
        <p class="text-muted mb-0">Discover local food businesses in Binalbagan.</p>
    </div>

    <form method="GET" action="{{ route('vendors.index') }}" role="search" class="mb-3">
        @if ($category)
            <input type="hidden" name="category" value="{{ $category }}">
        @endif
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="search" name="q" value="{{ $q }}" maxlength="100" class="form-control"
                   placeholder="Search food or vendors..." aria-label="Search food or vendors">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    {{-- Category chips --}}
    @if ($categories->isNotEmpty())
        <div class="be-chip-row mb-4">
            <a href="{{ route('vendors.index', array_filter(['q' => $q])) }}"
               class="be-cat-chip {{ $category === null ? 'active' : '' }}">All</a>
            @foreach ($categories as $name)
                <a href="{{ route('vendors.index', array_filter(['q' => $q, 'category' => $name])) }}"
                   class="be-cat-chip {{ $category === $name ? 'active' : '' }}">{{ $name }}</a>
            @endforeach
        </div>
    @endif

    @if ($q || $category)
        <p class="text-muted mb-3">
            {{ $vendors->total() }} {{ \Illuminate\Support\Str::plural('vendor', $vendors->total()) }} found
            @if ($q) for “{{ $q }}” @endif
            @if ($category) in {{ $category }} @endif
            &middot; <a href="{{ route('vendors.index') }}">Clear filters</a>
        </p>
    @endif

    @if ($vendors->isEmpty())
        <x-empty-state icon="bi-search" title="No vendors found"
                       text="Try a different search or category.">
            <a href="{{ route('vendors.index') }}" class="btn btn-primary">Show all vendors</a>
        </x-empty-state>
    @else
        <div class="row g-4">
            @foreach ($vendors as $vendor)
                <div class="col-sm-6 col-lg-4">
                    <x-vendor-card :vendor="$vendor" />
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $vendors->links() }}
        </div>
    @endif
</div>
@endsection