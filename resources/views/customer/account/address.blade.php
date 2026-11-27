@extends('layouts.app')

@section('title', 'Delivery address')

@section('content')
<div class="container py-4 py-lg-5">
    <h1 class="h3 mb-4">My account</h1>

    <div class="row g-4">
        <div class="col-lg-3"><x-account-nav /></div>

        <div class="col-lg-9">
            <form method="POST" action="{{ route('account.address.update') }}" class="be-card p-4" novalidate>
                @csrf
                @method('PUT')

                <h2 class="h5 mb-1">Delivery address</h2>
                <p class="text-muted small mb-4">This address is filled in for you at checkout and in Buy-for-Me requests. You can still change it for each order.</p>

                <div class="mb-3">
                    <label for="address" class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                    <textarea id="address" name="address" rows="3" maxlength="255" required
                              placeholder="House / street, purok, landmark"
                              class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="preferred_delivery_area" class="form-label fw-semibold">Preferred barangay</label>
                    <select id="preferred_delivery_area" name="preferred_delivery_area"
                            class="form-select @error('preferred_delivery_area') is-invalid @enderror">
                        <option value="">Not set</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->zone_name }}"
                                    @selected(old('preferred_delivery_area', $user->preferred_delivery_area) === $zone->zone_name)>
                                {{ $zone->zone_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('preferred_delivery_area')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">The barangay you usually order to.</div>
                </div>

                <button type="submit" class="btn btn-primary">Save address</button>
            </form>
        </div>
    </div>
</div>
@endsection