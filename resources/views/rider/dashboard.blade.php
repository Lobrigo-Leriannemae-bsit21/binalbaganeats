@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Rider Dashboard')
@section('page-title', 'Delivery Rider Portal')
@section('page-subtitle', 'Approved Service Zone: ' . $approvedArea)

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Available Delivery Jobs</small>
            <h3 class="fw-bold text-be-accent mb-0 mt-1">{{ $availableCount }}</h3>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Assigned Active Jobs</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $assignedCount }}</h3>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Assigned Errands</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $errandCount }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="be-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-be-primary mb-0">Your Active Assigned Deliveries</h6>
                <a href="{{ route('rider.available-jobs') }}" class="btn btn-sm btn-be-accent">View Available Jobs</a>
            </div>
            <div class="table-responsive">
                <table class="table be-table mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Vendor & Customer</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignedJobs as $job)
                            <tr>
                                <td class="fw-bold text-be-primary">{{ $job['order_number'] }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $job['vendor'] }}</div>
                                    <small class="text-be-muted">To: {{ $job['customer'] }} ({{ $job['address'] }})</small>
                                </td>
                                <td class="fw-bold">₱{{ number_format($job['delivery_fee'], 2) }}</td>
                                <td><x-badge :status="$job['status']" /></td>
                                <td>
                                    <a href="{{ route('rider.details', $job['order_number']) }}" class="btn btn-sm btn-be-primary">Update Status</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-be-muted">No assigned delivery jobs right now. Check Available Jobs.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="be-card p-4 bg-be-sage border-0">
            <h6 class="fw-bold text-be-primary mb-2"><i class="bi bi-shield-check text-be-primary me-1"></i> Zone Restriction Rule</h6>
            <p class="small text-be-muted mb-3">You can only view and accept delivery jobs and Buy-for-Me requests within your administrator-approved service zone.</p>
            <a href="{{ route('rider.buy-for-me') }}" class="btn btn-be-primary w-100">Browse Buy-for-Me Errands</a>
        </div>
    </div>
</div>
@endsection