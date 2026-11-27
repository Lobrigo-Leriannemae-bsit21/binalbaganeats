@extends('layouts.app')

@section('title', 'Buy-for-Me')

@section('content')
<div class="container py-4 py-lg-5">

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Buy-for-Me</h1>
            <p class="text-muted mb-0">Request items from any establishment and a rider will buy and deliver them to you.</p>
        </div>
        @if ($atLimit)
            <button type="button" class="btn btn-primary" disabled>Make a request</button>
        @else
            <a href="{{ route('buyforme.create') }}" class="btn btn-primary"><i class="bi bi-bag-plus me-1"></i> Make a request</a>
        @endif
    </div>

    @if ($atLimit)
        <div class="alert alert-warning">You have {{ $limit }} open requests. You can make a new one once one of them is finished.</div>
    @endif

    {{-- How it works --}}
    <div class="be-card p-4 mb-4">
        <h2 class="h6 text-uppercase text-muted mb-3">How it works</h2>
        <div class="row g-3 text-center">
            @foreach ([
                ['bi-pencil-square', 'Tell us what you need', 'Name the shop and the items.'],
                ['bi-person-check', 'A rider accepts', 'A rider in your barangay confirms the estimated cost.'],
                ['bi-phone', 'Pay '.$advancePercent.'% in advance', 'Send the advance by GCash so the rider can buy your items.'],
                ['bi-house-heart', 'Receive and settle', 'Pay the remaining balance when your items arrive.'],
            ] as [$icon, $title, $text])
                <div class="col-6 col-lg-3">
                    <div class="be-step-icon mx-auto mb-2"><i class="bi {{ $icon }}"></i></div>
                    <h3 class="h6 mb-1">{{ $title }}</h3>
                    <p class="small text-muted mb-0">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <h2 class="h5 mb-3">My requests</h2>

    @if ($requests->isEmpty())
        <x-empty-state icon="bi-bag-heart" title="No requests yet"
                       text="Need something from a shop that isn’t on BinalbaganEats? Make your first request." />
    @else
        @foreach ($requests as $item)
            <div class="be-card p-3 p-md-4 mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <a href="{{ route('buyforme.show', $item) }}" class="fw-bold text-dark text-decoration-none">{{ $item->request_code }}</a>
                        <div class="small text-muted">
                            {{ $item->establishment_name }} &middot; {{ $item->created_at->format('M j, Y g:i A') }}
                        </div>
                    </div>
                    <span class="be-badge {{ $item->request_status->badgeClass() }}">{{ $item->request_status->label() }}</span>
                </div>

                <p class="small text-muted mt-2 mb-0">{{ \Illuminate\Support\Str::limit($item->items_summary, 120) }}</p>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                    <div class="small text-muted">
                        <i class="bi bi-geo-alt"></i> {{ $item->deliveryZone?->zone_name }}
                        @if ($item->rider) &middot; Rider: {{ $item->rider->name }} @endif
                    </div>
                    <a href="{{ route('buyforme.show', $item) }}" class="btn btn-outline-primary btn-sm">View</a>
                </div>
            </div>
        @endforeach

        <div class="mt-4 d-flex justify-content-center">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection