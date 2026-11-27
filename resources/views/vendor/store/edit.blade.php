@extends('layouts.vendor')

@section('title', 'My store')

@section('content')
<h1 class="h3 mb-1">My store</h1>
<p class="text-muted mb-4">Manage how your store appears to customers.</p>

<div class="row g-4">
    <div class="col-xl-7">

        {{-- Store image --}}
        <section class="be-card overflow-hidden mb-4">
            <div class="be-banner">
                <img src="{{ $store->image_url }}" alt="{{ $store->business_name }}">
            </div>
            <div class="p-4">
                <h2 class="h5 mb-1">Store image</h2>
                <p class="text-muted small mb-3">Wide photos work best (about 1200 × 400). JPG, PNG or WEBP, up to 4 MB.</p>

                <form method="POST" action="{{ route('vendor.store.image') }}" enctype="multipart/form-data" class="mb-2">
                    @csrf
                    <div class="input-group">
                        <input type="file" name="store_image" required
                               accept="image/jpeg,image/png,image/webp"
                               class="form-control @error('store_image') is-invalid @enderror"
                               aria-label="Choose a store image">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                    @error('store_image')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </form>

                @if ($store->vendor_image)
                    <form method="POST" action="{{ route('vendor.store.image.destroy') }}"
                          data-confirm="Remove your store image?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger p-0">
                            <i class="bi bi-trash"></i> Remove image
                        </button>
                    </form>
                @endif
            </div>
        </section>

        {{-- Store information --}}
        <form method="POST" action="{{ route('vendor.store.update') }}" class="be-card p-4 mb-4" novalidate>
            @csrf
            @method('PUT')

            <h2 class="h5 mb-3">Store information</h2>

            <x-form.input name="business_name" label="Store name" :value="$store->business_name"
                          :required="true" maxlength="255" />

            <x-form.input name="business_address" label="Store address" :value="$store->business_address"
                          :required="true" maxlength="255" />

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Description</label>
                <textarea id="description" name="description" rows="3" maxlength="1000"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="What makes your store special?">{{ old('description', $store->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <x-form.input name="estimated_prep_time" label="Usual preparation time (minutes)" type="number"
                          :value="$store->estimated_prep_time" :required="true" min="5" max="180" inputmode="numeric"
                          help="Shown to customers and used for their delivery estimate." />

            <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
    </div>

    <div class="col-xl-5">

        {{-- Availability --}}
        <section class="be-card p-4 mb-4">
            <h2 class="h5 mb-3">Store availability</h2>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span>Customers can currently</span>
                <span class="be-badge {{ $store->is_available ? 'be-badge-success' : 'be-badge-danger' }}">
                    {{ $store->is_available ? 'order from you' : 'only browse' }}
                </span>
            </div>

            <form method="POST" action="{{ route('vendor.store.availability') }}"
                  @if ($store->is_available) data-confirm="Close your store? Customers will not be able to place new orders." @endif>
                @csrf
                @method('PATCH')
                <input type="hidden" name="is_available" value="{{ $store->is_available ? 0 : 1 }}">
                <button type="submit" class="btn {{ $store->is_available ? 'btn-outline-danger' : 'btn-primary' }} w-100">
                    {{ $store->is_available ? 'Close store' : 'Open store' }}
                </button>
            </form>

            <p class="small text-muted mt-3 mb-0">Orders already placed are not affected when you close the store.</p>
        </section>

        {{-- GCash --}}
        <form method="POST" action="{{ route('vendor.store.payment') }}" class="be-card p-4" novalidate>
            @csrf
            @method('PUT')

            <h2 class="h5 mb-1">GCash payments</h2>
            <p class="text-muted small mb-3">
                Customers see these details at checkout and send their payment here. You review each payment before preparing the order.
                Leave both fields empty to accept Cash on Delivery only.
            </p>

            <x-form.input name="gcash_number" label="GCash number" type="tel" :value="$store->gcash_number"
                          placeholder="09171234567" autocomplete="off" />

            <x-form.input name="gcash_name" label="Name on the GCash account" :value="$store->gcash_name"
                          maxlength="255" autocomplete="off" />

            <button type="submit" class="btn btn-primary">Save GCash details</button>
        </form>
    </div>
</div>
@endsection