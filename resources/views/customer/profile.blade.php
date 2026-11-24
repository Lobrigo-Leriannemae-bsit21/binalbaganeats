@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'My Profile')
@section('page-title', 'Customer Profile')
@section('page-subtitle', 'Manage your personal contact and default delivery address')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="be-card p-4">
            <h5 class="fw-bold text-be-primary mb-3">Personal & Delivery Information</h5>
            <form method="POST" action="{{ route('customer.profile.update') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" value="{{ $user->email }}" class="form-control bg-light" disabled>
                    <div class="form-text">Email address is tied to your login credentials and cannot be changed here.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mobile Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Default Delivery Address in Binalbagan</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-be-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Save Profile Changes
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="be-card p-4 bg-be-sage border-0">
            <h5 class="fw-bold text-be-primary mb-3">Account Summary</h5>
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10">
                <span class="text-be-muted">Account Role</span>
                <span class="fw-bold text-be-primary text-capitalize">{{ $user->role }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom border-secondary border-opacity-10">
                <span class="text-be-muted">Account Status</span>
                <x-badge :status="ucfirst($user->status)" />
            </div>
            <div class="d-flex justify-content-between py-2">
                <span class="text-be-muted">Member Since</span>
                <span class="fw-semibold">{{ $user->created_at?->format('M d, Y') ?? 'Nov 2026' }}</span>
            </div>
        </div>
    </div>
</div>
@endsection