@extends('layouts.app')

@section('title', 'New Buy-for-Me request')

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('buyforme.index') }}">Buy-for-Me</a></li>
                    <li class="breadcrumb-item active" aria-current="page">New request</li>
                </ol>
            </nav>

            <h1 class="h3 mb-4">New Buy-for-Me request</h1>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">Please check the highlighted fields and try again.</div>
            @endif

            <form method="POST" action="{{ route('buyforme.store') }}" id="buyforme-form" novalidate>
                @csrf
                <input type="hidden" name="form_token" value="{{ $token }}">

                <section class="be-card p-4 mb-4">
                    <h2 class="h5 mb-3"><span class="be-num">1</span> What should we buy?</h2>

                    <x-form.input name="establishment_name" label="Shop or establishment" :required="true"
                                  maxlength="255" placeholder="e.g. Jollibee, a pharmacy, a bakery" />

                    <div class="mb-0">
                        <label for="items_summary" class="form-label fw-semibold">
                            Items you want <span class="text-danger">*</span>
                        </label>
                        <textarea id="items_summary" name="items_summary" rows="5" maxlength="2000" required
                                  placeholder="One item per line, with quantity and any details&#10;e.g. 2 pcs Chickenjoy with rice&#10;1 large Coke"
                                  class="form-control @error('items_summary') is-invalid @enderror">{{ old('items_summary') }}</textarea>
                        @error('items_summary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">The rider will confirm the estimated cost before anything is bought.</div>
                    </div>
                </section>

                <section class="be-card p-4 mb-4">
                    <h2 class="h5 mb-3"><span class="be-num">2</span> Where should we deliver?</h2>

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

                    <div class="mb-0">
                        <label for="delivery_zone_id" class="form-label fw-semibold">
                            Your barangay <span class="text-danger">*</span>
                        </label>
                        <select id="delivery_zone_id" name="delivery_zone_id" required
                                class="form-select @error('delivery_zone_id') is-invalid @enderror">
                            <option value="" disabled @selected(! old('delivery_zone_id'))>Choose your barangay…</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}" @selected((int) old('delivery_zone_id') === $zone->id)>
                                    {{ $zone->zone_name }} — delivery fee ₱{{ number_format($zone->delivery_fee, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('delivery_zone_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">The delivery fee is the same as for food orders in your barangay.</div>
                    </div>
                </section>

                <div class="be-inset p-3 mb-4 small">
                    <strong>How payment works:</strong> after a rider accepts, they confirm the estimated cost.
                    You then pay <strong>{{ $advancePercent }}%</strong> of the estimated total (items + delivery fee) in advance by GCash.
                    The rider buys your items, and you pay the remaining balance when they arrive.
                    You can cancel any time before you send the advance.
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100" id="buyforme-submit">Submit request</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Prevent accidental double submission.
        document.getElementById('buyforme-form').addEventListener('submit', function () {
            var button = document.getElementById('buyforme-submit');
            button.disabled = true;
            button.textContent = 'Submitting…';
        });
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                var button = document.getElementById('buyforme-submit');
                button.disabled = false;
                button.textContent = 'Submit request';
            }
        });
    </script>
@endpush