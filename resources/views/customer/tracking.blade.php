@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'Order Tracking & ETA')
@section('page-title', 'Order Tracking & Zone-Based ETA')
@section('page-subtitle', 'Recorded status progression and simplified arrival estimate')

@section('content')
@if (!$selectedOrder)
    <div class="be-card p-5 text-center">
        <h5 class="text-be-primary">No orders available to track.</h5>
    </div>
@else
    @php
        $stages = ['Pending', 'Payment Verification', 'Accepted', 'Preparing', 'Ready for Pickup', 'Assigned', 'Picked Up', 'Delivered'];
        $currentIndex = array_search($selectedOrder['status'], $stages);
        if ($currentIndex === false) $currentIndex = 0;
        $totalEta = $selectedOrder['prep_mins'] + $selectedOrder['travel_mins'] + $selectedOrder['dispatch_buffer'];
    @endphp

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="be-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-be-primary mb-0">Order #{{ $selectedOrder['order_number'] }}</h5>
                        <small class="text-be-muted">{{ $selectedOrder['vendor'] }} &bull; {{ $selectedOrder['zone'] }}</small>
                    </div>
                    <x-badge :status="$selectedOrder['status']" />
                </div>

                {{-- Recorded Status Timeline --}}
                <div class="list-group list-group-flush mt-3">
                    @foreach ($stages as $idx => $stage)
                        <div class="list-group-item px-0 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge rounded-circle {{ $idx <= $currentIndex ? 'bg-be-primary' : 'bg-secondary-subtle text-secondary' }}" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                    @if ($idx <= $currentIndex) <i class="bi bi-check"></i> @else {{ $idx + 1 }} @endif
                                </span>
                                <span class="{{ $idx === $currentIndex ? 'fw-bold text-be-accent' : ($idx < $currentIndex ? 'fw-semibold text-be-primary' : 'text-be-muted') }}">
                                    {{ $stage }}
                                </span>
                            </div>
                            @if ($idx === $currentIndex)
                                <span class="badge bg-be-sage text-be-primary">Current Recorded Stage</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="be-card p-4 mb-4 bg-be-sage border-0">
                <h6 class="fw-bold text-be-primary mb-2"><i class="bi bi-stopwatch text-be-accent me-1"></i> Estimated Time of Arrival (ETA)</h6>
                <h2 class="fw-extrabold text-be-accent mb-2">~{{ $totalEta }} mins</h2>
                <ul class="small text-be-muted ps-3 mb-2">
                    <li>Vendor Est. Prep Time: <strong>{{ $selectedOrder['prep_mins'] }} mins</strong></li>
                    <li>Zone Travel Estimate: <strong>{{ $selectedOrder['travel_mins'] }} mins</strong></li>
                    <li>Fixed Dispatch Buffer: <strong>{{ $selectedOrder['dispatch_buffer'] }} mins</strong></li>
                </ul>
                <small class="text-be-muted fst-italic d-block">Note: ETA is a simplified estimate based on recorded status updates, not live GPS tracking.</small>
            </div>

            <div class="be-card p-4">
                <h6 class="fw-bold text-be-primary mb-2">Assigned Rider</h6>
                <p class="mb-3"><i class="bi bi-scooter text-be-accent me-1"></i> {{ $selectedOrder['rider'] }}</p>

                <label class="form-label small fw-semibold">Switch Tracked Order</label>
                <select class="form-select form-select-sm" onchange="window.location.href=this.value">
                    @foreach ($orders as $ord)
                        <option value="{{ route('customer.tracking', $ord['order_number']) }}" {{ $ord['order_number'] === $selectedOrder['order_number'] ? 'selected' : '' }}>
                            {{ $ord['order_number'] }} ({{ $ord['status'] }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
@endsection