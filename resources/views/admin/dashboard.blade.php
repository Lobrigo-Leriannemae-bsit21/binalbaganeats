@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Admin Dashboard')
@section('page-title', 'System Administrator Dashboard')
@section('page-subtitle', 'Platform oversight, account approvals, and delivery zone governance')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Pending Account Approvals</small>
            <h3 class="fw-bold text-be-accent mb-0 mt-1">{{ $pendingCount }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Approved Local Vendors</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $approvedVendors }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Approved Delivery Riders</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $approvedRiders }}</h3>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Total Registered Users</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ $totalUsers }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="be-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-be-primary mb-0">Pending Vendor & Rider Registrations</h6>
                <a href="{{ route('admin.approvals') }}" class="btn btn-sm btn-be-accent">Review Approval Queue</a>
            </div>
            <div class="table-responsive">
                <table class="table be-table mb-0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Role</th>
                            <th>Service Area / Store</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingUsers as $applicant)
                            <tr>
                                <td>
                                    <div class="fw-bold text-be-primary">{{ $applicant->name }}</div>
                                    <small class="text-be-muted">{{ $applicant->email }}</small>
                                </td>
                                <td class="text-capitalize">{{ $applicant->role }}</td>
                                <td class="small">{{ $applicant->role === 'rider' ? $applicant->preferred_delivery_area : $applicant->storefront_name }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.approvals.approve', $applicant->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-be-muted">All vendor and rider applications have been reviewed.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="be-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-be-primary mb-0">Recent System Activity Logs</h6>
                <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-outline-be-primary">All Logs</a>
            </div>
            <div class="list-group list-group-flush">
                @foreach ($logs as $log)
                    <div class="list-group-item px-0 py-2">
                        <div class="small fw-semibold text-be-primary">{{ $log['action'] }}</div>
                        <small class="text-be-muted">{{ $log['actor'] }} &bull; {{ $log['timestamp'] }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection