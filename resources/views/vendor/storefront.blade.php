@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Storefront Management')
@section('page-title', 'Storefront Profile & Availability')
@section('page-subtitle', 'Configure your digital storefront, operating status, and preparation-time estimate')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="be-card p-4">
            <form method="POST" action="{{ route('vendor.storefront.update') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Storefront Name</label>
                        <input type="text" name="name" value="{{ $storefront['name'] }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Storefront Availability</label>
                        <select name="status" class="form-select">
                            <option value="Available" {{ $storefront['status'] === 'Available' ? 'selected' : '' }}>Available (Open)</option>
                            <option value="Unavailable" {{ $storefront['status'] === 'Unavailable' ? 'selected' : '' }}>Unavailable (Closed)</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cuisine Category</label>
                        <input type="text" name="category" value="{{ $storefront['category'] }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Default Preparation Time Estimate (Mins)</label>
                        <input type="number" name="prep_time" value="{{ $storefront['prep_time'] }}" min="5" max="120" class="form-control" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Location in Barangay San Pedro</label>
                        <input type="text" name="address" value="{{ $storefront['address'] }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Operating Hours</label>
                        <input type="text" name="operating_hours" value="{{ $storefront['operating_hours'] }}" class="form-control" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vendor GCash Number (Displayed to Customers)</label>
                        <input type="text" name="gcash_number" value="{{ $storefront['gcash_number'] }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">GCash Account Name</label>
                        <input type="text" name="gcash_name" value="{{ $storefront['gcash_name'] }}" class="form-control" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Storefront Description</label>
                    <textarea name="description" rows="3" class="form-control" required>{{ $storefront['description'] }}</textarea>
                </div>

                <button type="submit" class="btn btn-be-primary px-4">Save Storefront Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection