@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'Checkout')
@section('page-title', 'Checkout & Order Placement')
@section('page-subtitle', 'Select delivery zone, payment method, and submit your order')

@section('content')
<form method="POST" action="{{ route('customer.checkout.place') }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="be-card p-4 mb-4">
                <h5 class="fw-bold text-be-primary mb-3">1. Delivery Zone & Address</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Serviceable Delivery Zone in Binalbagan</label>
                    <select name="zone_id" id="zoneSelect" class="form-select" required>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone['id'] }}" data-fee="{{ $zone['fee'] }}" data-mins="{{ $zone['travel_mins'] }}">
                                {{ $zone['name'] }} — ₱{{ number_format($zone['fee'], 2) }} (~{{ $zone['travel_mins'] }} mins travel)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold">Exact Delivery Landmark / Street Address</label>
                    <input type="text" name="delivery_address" value="{{ auth()->user()->address }}" class="form-control" required>
                </div>
            </div>

            <div class="be-card p-4">
                <h5 class="fw-bold text-be-primary mb-3">2. Payment Method</h5>
                <div class="mb-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="payCod" value="Cash on Delivery" checked>
                        <label class="form-check-label fw-semibold" for="payCod">
                            Cash on Delivery (COD) — Pay rider upon arrival
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="payGcash" value="GCash">
                        <label class="form-check-label fw-semibold" for="payGcash">
                            GCash (External Payment + Reference Submission)
                        </label>
                    </div>
                </div>

                <div id="gcashDetailsBox" class="bg-be-sage p-3 rounded-3 d-none">
                    <p class="small text-be-primary fw-semibold mb-2">
                        <i class="bi bi-info-circle me-1"></i> GCash transactions are completed outside the system. Enter your GCash reference number now or submit it on the My Orders page for manual vendor verification.
                    </p>
                    <label class="form-label small fw-semibold">GCash Reference Number (Optional now, required before prep)</label>
                    <input type="text" name="gcash_ref" class="form-control" placeholder="e.g., GC-908471235">
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="be-card p-4">
                <h5 class="fw-bold text-be-primary mb-3">Order Summary</h5>
                @foreach ($cart as $item)
                    <div class="d-flex justify-content-between small mb-2">
                        <span>{{ $item['qty'] }}x {{ $item['name'] }}</span>
                        <span class="fw-semibold">₱{{ number_format($item['price'] * $item['qty'], 2) }}</span>
                    </div>
                @endforeach

                <hr>

                @if ($sukiCredit > 0)
                    <div class="form-check bg-be-sage p-2 px-3 rounded mb-3">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="apply_suki_credit" value="1" id="applyCredit">
                        <label class="form-check-label small fw-semibold text-be-primary" for="applyCredit">
                            Apply Redeemed Suki Credit (-₱{{ number_format($sukiCredit, 2) }})
                        </label>
                    </div>
                @endif

                <div class="d-flex justify-content-between mb-1">
                    <span class="text-be-muted">Subtotal</span>
                    <span id="summarySubtotal" data-val="{{ $subtotal }}">₱{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-be-muted">Zone Delivery Fee</span>
                    <span id="summaryFee">₱25.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Suki Discount</span>
                    <span id="summaryDiscount">-₱0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fs-5 fw-bold text-be-primary mb-4">
                    <span>Total Amount Due</span>
                    <span id="summaryTotal">₱{{ number_format($subtotal + 25, 2) }}</span>
                </div>

                <button type="submit" class="btn btn-be-accent w-100 py-2">
                    <i class="bi bi-check2-circle me-1"></i> Place Food Order
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const payCod = document.getElementById('payCod');
    const payGcash = document.getElementById('payGcash');
    const gcashBox = document.getElementById('gcashDetailsBox');
    const zoneSelect = document.getElementById('zoneSelect');
    const applyCredit = document.getElementById('applyCredit');

    const subtotal = parseFloat(document.getElementById('summarySubtotal').dataset.val);
    const creditVal = {{ (float) $sukiCredit }};

    function togglePayment() {
        gcashBox.classList.toggle('d-none', !payGcash.checked);
    }

    function recalc() {
        const selected = zoneSelect.options[zoneSelect.selectedIndex];
        const fee = parseFloat(selected.dataset.fee);
        const discount = (applyCredit && applyCredit.checked) ? Math.min(subtotal, creditVal) : 0;
        const total = Math.max(0, subtotal + fee - discount);

        document.getElementById('summaryFee').textContent = '₱' + fee.toFixed(2);
        document.getElementById('summaryDiscount').textContent = '-₱' + discount.toFixed(2);
        document.getElementById('summaryTotal').textContent = '₱' + total.toFixed(2);
    }

    payCod.addEventListener('change', togglePayment);
    payGcash.addEventListener('change', togglePayment);
    zoneSelect.addEventListener('change', recalc);
    if (applyCredit) applyCredit.addEventListener('change', recalc);
    recalc();
});
</script>
@endpush