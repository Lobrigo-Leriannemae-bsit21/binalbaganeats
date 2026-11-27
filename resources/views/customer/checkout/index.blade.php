@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4 py-lg-5">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <h1 class="h3 mb-4">Checkout</h1>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">Please check the highlighted fields and try again.</div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data"
          data-checkout
          data-subtotal="{{ number_format($subtotal, 2, '.', '') }}"
          data-point-value="{{ $suki['valuePesos'] }}"
          data-min-points="{{ $suki['min'] }}"
          data-max-points="{{ $suki['max'] }}">
        @csrf
        <input type="hidden" name="checkout_token" value="{{ $token }}">

        <div class="row g-4">
            <div class="col-lg-7">

                {{-- 1. Delivery information --}}
                <section class="be-card p-4 mb-4">
                    <h2 class="h5 mb-3"><span class="be-num">1</span> Delivery information</h2>

                    <div class="mb-3">
                        <label for="delivery_address" class="form-label fw-semibold">
                            Delivery address <span class="text-danger">*</span>
                        </label>
                        <textarea id="delivery_address" name="delivery_address" rows="2" maxlength="255" required
                                  placeholder="House / street, purok, landmark"
                                  class="form-control @error('delivery_address') is-invalid @enderror">{{ old('delivery_address', $user->address) }}</textarea>
                        @error('delivery_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="contact_phone">Contact number</label>
                        <input type="text" id="contact_phone" class="form-control" value="{{ $user->phone }}" readonly>
                        <div class="form-text">The rider will call this number. It comes from your account.</div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="save_address" id="save_address" value="1"
                               @checked(old('save_address'))>
                        <label class="form-check-label" for="save_address">Save this address to my account</label>
                    </div>
                </section>

                {{-- 2. Delivery zone (one per barangay) --}}
                <section class="be-card p-4 mb-4">
                    <h2 class="h5 mb-3"><span class="be-num">2</span> Delivery zone</h2>

                    @if ($zones->isEmpty())
                        <div class="alert alert-warning mb-0">
                            No delivery zones are available right now, so orders can’t be placed yet. Please try again later.
                        </div>
                    @else
                        <label for="delivery_zone_id" class="form-label fw-semibold">
                            Your barangay <span class="text-danger">*</span>
                        </label>
                        <select id="delivery_zone_id" name="delivery_zone_id" required
                                class="form-select @error('delivery_zone_id') is-invalid @enderror">
                            <option value="" disabled @selected(! old('delivery_zone_id'))>Choose your barangay…</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}"
                                        data-fee="{{ number_format($zone->delivery_fee, 2, '.', '') }}"
                                        data-time="{{ $zone->estimated_travel_time }}"
                                        data-distance="{{ $zone->distance_range }}"
                                        @selected((int) old('delivery_zone_id') === $zone->id)>
                                    {{ $zone->zone_name }} — ₱{{ number_format($zone->delivery_fee, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text" data-zone-hint>Delivery fees are set per barangay.</div>
                        @error('delivery_zone_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    @endif
                </section>

                {{-- 3. Suki Points --}}
                <section class="be-card p-4 mb-4">
                    <h2 class="h5 mb-3"><span class="be-num">3</span> Suki Points</h2>

                    <p class="mb-2">
                        You have <strong>{{ number_format($suki['balance']) }} points</strong>
                        (≈ ₱{{ number_format($suki['balance'] * $suki['valuePesos'], 2) }}).
                    </p>

                    @if ($suki['max'] >= $suki['min'])
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="use_suki" data-points-toggle
                                   @checked((int) old('use_points') > 0)>
                            <label class="form-check-label" for="use_suki">Use my Suki Points on this order</label>
                        </div>

                        <div class="{{ (int) old('use_points') > 0 ? '' : 'd-none' }}" data-points-panel>
                            <label for="use_points" class="form-label fw-semibold">Points to use</label>
                            <div class="input-group" style="max-width: 320px;">
                                <input type="number" id="use_points" name="use_points"
                                       value="{{ old('use_points', 0) }}"
                                       min="{{ $suki['min'] }}" max="{{ $suki['max'] }}" step="1" inputmode="numeric"
                                       class="form-control @error('use_points') is-invalid @enderror">
                                <button type="button" class="btn btn-outline-primary" data-points-max>Use max</button>
                            </div>
                            @error('use_points')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Each point is ₱{{ rtrim(rtrim(number_format($suki['valuePesos'], 2), '0'), '.') }} off.
                                You can use {{ $suki['min'] }} to {{ number_format($suki['max']) }} points on this order
                                (up to {{ $suki['percent'] }}% of the food, never the delivery fee).
                            </div>
                        </div>
                    @elseif ($suki['balance'] < $suki['min'])
                        <p class="text-muted small mb-0">You need at least {{ $suki['min'] }} points to redeem.</p>
                    @else
                        <p class="text-muted small mb-0">This order is too small to use points (they can cover up to {{ $suki['percent'] }}% of the food subtotal).</p>
                    @endif

                    @if ($suki['earnable'] > 0)
                        <p class="small text-success mt-3 mb-0">
                            <i class="bi bi-gift"></i> You’ll earn <strong>{{ $suki['earnable'] }} Suki {{ \Illuminate\Support\Str::plural('Point', $suki['earnable']) }}</strong> once this order is delivered.
                        </p>
                    @endif
                </section>

                {{-- 4. Payment --}}
                <section class="be-card p-4 mb-4">
                    <h2 class="h5 mb-3"><span class="be-num">4</span> Payment method</h2>

                    <div class="row g-2">
                        <div class="col-12">
                            <input type="radio" class="btn-check" name="payment_method" id="pay-cod" value="cod"
                                   @checked(old('payment_method', 'cod') === 'cod') required>
                            <label class="be-choice" for="pay-cod">
                                <span class="fw-semibold"><i class="bi bi-cash-coin me-1"></i> Cash on Delivery</span><br>
                                <span class="small text-muted">Pay the rider in cash when your order arrives.</span>
                            </label>
                        </div>

                        <div class="col-12">
                            <input type="radio" class="btn-check" name="payment_method" id="pay-gcash" value="gcash"
                                   @checked(old('payment_method') === 'gcash')
                                   @disabled(! $gcashAvailable)>
                            <label class="be-choice" for="pay-gcash">
                                <span class="fw-semibold"><i class="bi bi-phone me-1"></i> GCash</span><br>
                                <span class="small text-muted">
                                    @if ($gcashAvailable)
                                        Send the payment to the vendor, then upload your receipt. The vendor verifies it manually.
                                    @else
                                        This vendor does not accept GCash.
                                    @endif
                                </span>
                            </label>
                        </div>
                    </div>

                    @error('payment_method')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror

                    @if ($gcashAvailable)
                        <div class="be-inset p-3 mt-3 {{ old('payment_method') === 'gcash' ? '' : 'd-none' }}" data-gcash-panel>
                            <p class="mb-2">
                                <strong>Step 1.</strong> Send <strong data-total-display>₱{{ number_format($subtotal, 2) }}</strong> via GCash to:
                            </p>
                            <div class="be-gcash-box mb-3">
                                <div class="fw-semibold">{{ $vendor->gcash_name ?: $vendor->business_name }}</div>
                                <div class="fs-5 fw-bold">{{ $vendor->gcash_number }}</div>
                            </div>

                            <p class="mb-2"><strong>Step 2.</strong> Enter the reference number from your receipt and upload a screenshot.</p>

                            <x-form.input name="transaction_reference" label="GCash reference number"
                                          data-gcash-field maxlength="40" autocomplete="off"
                                          placeholder="e.g. 1234567890123"
                                          help="8 to 30 letters or numbers. Each reference can only be used once." />

                            <div>
                                <label for="payment_proof" class="form-label fw-semibold">Payment screenshot</label>
                                <input type="file" id="payment_proof" name="payment_proof" data-gcash-field
                                       accept="image/jpeg,image/png,image/webp"
                                       class="form-control @error('payment_proof') is-invalid @enderror">
                                <div class="form-text">JPG, PNG or WEBP, up to 4 MB.</div>
                                @error('payment_proof')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            {{-- Summary --}}
            <div class="col-lg-5">
                <div class="be-card p-4 be-sticky-summary">
                    <h2 class="h5 mb-1">Order summary</h2>
                    <p class="small text-muted mb-3">from <strong>{{ $vendor->business_name }}</strong></p>

                    @foreach ($lines as $line)
                        <div class="d-flex justify-content-between gap-2 small mb-2">
                            <span>{{ $line['quantity'] }} &times; {{ $line['item']->item_name }}</span>
                            <span>₱{{ number_format($line['line_total'], 2) }}</span>
                        </div>
                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span><strong>₱{{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery fee</span><strong data-fee-display>Select a zone</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success d-none" data-discount-row>
                        <span>Suki Points discount</span><strong data-discount-display>−₱0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between fs-5 pt-2 border-top">
                        <strong>Total</strong><strong class="be-price" data-total-display>₱{{ number_format($subtotal, 2) }}</strong>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-3" data-submit-once
                            @disabled($zones->isEmpty())>
                        Place order
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-link w-100 mt-1">Back to cart</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection