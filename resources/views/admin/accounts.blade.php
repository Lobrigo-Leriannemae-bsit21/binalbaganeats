@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Account Management')
@section('page-title', 'User Account Management')
@section('page-subtitle', 'Search and filter registered customers, local food vendors, and delivery riders')

@section('content')
<div class="be-card p-3 mb-4">
    <form method="GET" action="{{ route('admin.accounts') }}" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search name or email...">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">All Roles</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="vendor" {{ request('role') === 'vendor' ? 'selected' : '' }}>Local Food Vendor</option>
                <option value="rider" {{ request('role') === 'rider' ? 'selected' : '' }}>Delivery Rider</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>System Administrator</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-be-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.accounts') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Phone & Address</th>
                    <th>Service Area / Store</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td>
                            <div class="fw-bold text-be-primary">{{ $u->name }}</div>
                            <small class="text-be-muted">{{ $u->email }}</small>
                        </td>
                        <td class="text-capitalize fw-semibold">{{ $u->role }}</td>
                        <td class="small">{{ $u->phone }}<br><span class="text-be-muted">{{ $u->address }}</span></td>
                        <td class="small">{{ $u->preferred_delivery_area ?? $u->storefront_name ?? '—' }}</td>
                        <td><x-badge :status="ucfirst($u->status)" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection