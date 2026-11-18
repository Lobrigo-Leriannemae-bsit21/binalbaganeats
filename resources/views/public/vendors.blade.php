@extends('layouts.app')

@section('title', 'Browse Vendors — BinalbaganEats')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-be-primary mb-1">Participating Vendors</h3>
            <p class="text-be-muted mb-0">Approved local food stalls and carinderias in Barangay San Pedro</p>
        </div>
        <form method="GET" action="{{ route('public.vendors') }}" class="d-flex gap-2">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search vendor or cuisine...">
            <button type="submit" class="btn btn-be-primary">Search</button>
        </form>
    </div>

    @if (empty($vendors))
        <div class="be-card p-5 text-center">
            <i class="bi bi-shop-window fs-1 text-be-muted"></i>
            <h5 class="mt-3 text-be-primary">No matching vendors found</h5>
            <a href="{{ route('public.vendors') }}" class="btn btn-outline-be-primary mt-2">Reset Filter</a>
        </div>
    @else
        <div class="row g-4">
            @foreach ($vendors as $vendor)
                <div class="col-md-4">
                    <div class="be-card h-100 p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-be-sage text-be-primary">{{ $vendor['barangay'] }}</span>
                            <x-badge :status="$vendor['status']" />
                        </div>
                        <h5 class="fw-bold text-be-primary">{{ $vendor['name'] }}</h5>
                        <p class="small text-be-muted flex-grow-1">{{ $vendor['description'] }}</p>
                        <div class="small text-be-muted mb-3">
                            <i class="bi bi-clock text-be-accent me-1"></i> Est. Prep: {{ $vendor['prep_time'] }}
                        </div>
                        <a href="{{ route('public.vendors.show', $vendor['id']) }}" class="btn btn-be-accent w-100">
                            Browse Menu Items
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection