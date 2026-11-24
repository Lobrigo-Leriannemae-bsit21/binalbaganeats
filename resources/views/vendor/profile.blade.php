@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Vendor Profile')
@section('page-title', 'Vendor Account Profile')
@section('page-subtitle', 'Manage owner contact details')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="be-card p-4">
            <form method="POST" action="{{ route('vendor.profile.update') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Owner / Representative Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" value="{{ $user->email }}" class="form-control bg-light" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Contact Number</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Business Address</label>
                    <input type="text" name="address" value="{{ $user->address }}" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-be-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection