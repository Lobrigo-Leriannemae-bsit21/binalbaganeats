@extends('layouts.app')

@section('title', 'Create Account — BinalbaganEats')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="be-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-be-primary mb-1">Join BinalbaganEats</h3>
                    <p class="text-be-muted small">Create a Customer, Local Food Vendor, or Delivery Rider account</p>
                </div>

                <form method="POST" action="{{ route('register.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Account Role</label>
                        <select name="role" id="roleSelector" class="form-select" required>
                            <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer (Instant Access)</option>
                            <option value="vendor" {{ old('role') === 'vendor' ? 'selected' : '' }}>Local Food Vendor (Requires Admin Approval)</option>
                            <option value="rider" {{ old('role') === 'rider' ? 'selected' : '' }}>Delivery Rider (Requires Admin Approval)</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="vendorStorefrontGroup">
                        <label class="form-label fw-semibold">Storefront / Carinderia Name <span class="text-danger">*</span></label>
                        <input type="text" name="storefront_name" value="{{ old('storefront_name') }}" class="form-control" placeholder="e.g., Nanay Rosa's Lutong Bahay">
                    </div>

                    <div class="mb-3 d-none" id="riderAreaGroup">
                        <label class="form-label fw-semibold">Preferred Delivery Area <span class="text-danger">*</span></label>
                        <select name="preferred_delivery_area" class="form-select">
                            <option value="">-- Choose Preferred Service Zone --</option>
                            @foreach ($deliveryAreas as $area)
                                <option value="{{ $area }}" {{ old('preferred_delivery_area') === $area ? 'selected' : '' }}>
                                    {{ $area }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Riders can only accept delivery requests inside their administrator-approved delivery zone.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Juan Dela Cruz" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="09XX-XXX-XXXX" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="juan@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Complete Address in Binalbagan</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="form-control" placeholder="Barangay / Street / Landmark" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 8 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-be-accent w-100 py-2">
                        Create Account
                    </button>
                </form>

                <p class="text-center small text-be-muted mt-4 mb-0">
                    Already registered? <a href="{{ route('login') }}" class="fw-bold text-be-primary text-decoration-none">Log in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelector = document.getElementById('roleSelector');
    const vendorGroup = document.getElementById('vendorStorefrontGroup');
    const riderGroup = document.getElementById('riderAreaGroup');

    function toggleRoleFields() {
        const role = roleSelector.value;
        vendorGroup.classList.toggle('d-none', role !== 'vendor');
        riderGroup.classList.toggle('d-none', role !== 'rider');
    }

    roleSelector.addEventListener('change', toggleRoleFields);
    toggleRoleFields();
});
</script>
@endpush