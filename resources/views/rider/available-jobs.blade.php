@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Available Jobs')
@section('page-title', 'Available Delivery Requests')
@section('page-subtitle', 'First eligible rider to accept gets the assignment (UC-11)')

@section('content')
<div class="row g-3">
    @forelse ($jobs as $job)
        <div class="col-md-6">
            <div class="be-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-be-primary">{{ $job['order_number'] }}</span>
                        <x-badge :status="$job['status']" />
                    </div>
                    <h6 class="fw-bold text-be-primary mb-1">{{ $job['vendor'] }}</h6>
                    <p class="small text-be-muted mb-2">Deliver to: <strong>{{ $job['customer'] }}</strong> &bull; {{ $job['address'] }}</p>
                    <div class="small text-be-muted mb-3">Items: {{ $job['items'] }}</div>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-2">
                    <span class="fs-5 fw-bold text-be-accent">Delivery Fee: ₱{{ number_format($job['delivery_fee'], 2) }}</span>
                    <form method="POST" action="{{ route('rider.jobs.accept', $job['order_number']) }}">
                        @csrf
                        <button type="submit" class="btn btn-be-accent">Accept Delivery Job</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="be-card p-5 text-center">
                <h5 class="text-be-primary">No available delivery requests in your zone right now.</h5>
            </div>
        </div>
    @endforelse
</div>
@endsection