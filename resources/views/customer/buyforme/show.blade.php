@extends('layouts.app')

@section('title', 'Request '.$buyForMe->request_code)

@section('content')
@php
    $status = $buyForMe->request_status;
    $settlement = $buyForMe->settlement_status;
    $rider = $buyForMe->rider;
    $peso = fn (?int $cents) => '₱'.number_format(($cents ?? 0) / 100, 2);
    $hasEstimate = $buyForMe->estimated_expense !== null;
    $hasActual = $buyForMe->actual_expense !== null;
    $remaining = $buyForMe->remainingCents();
    $advanceCents = $buyForMe->advanceCents();
    $advancePaid = $buyForMe->advanceConfirmed();
@endphp

<div class="container py-4 py-lg-5">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('buyforme.index') }}">Buy-for-Me</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $buyForMe->request_code }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Request {{ $buyForMe->request_code }}</h1>
            <p class="text-muted mb-0">{{ $buyForMe->establishment_name }} &middot; submitted {{ $buyForMe->created_at->format('M j, Y g:i A') }}</p>
        </div>
        <span class="be-badge {{ $status->badgeClass() }} fs-6">{{ $status->label() }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">

            {{-- Tracker --}}
            <div class="be-card p-4 mb-4">
                <x-buyforme-stepper :status="$status" />

                @unless ($status->isFinal())
                    <p class="small text-muted mt-4 mb-0">
                        <i class="bi bi-arrow-repeat"></i> This page refreshes automatically.
                        <a href="{{ route('buyforme.show', $buyForMe) }}">Refresh now</a>
                    </p>
                @endunless
            </div>

            {{-- Advance payment --}}
            @if ($status === \App\Enums\BuyForMeStatus::AwaitingAdvance && $settlement === \App\Enums\BuyForMeSettlement::AwaitingAdvance)
                <div class="be-card p-4 mb-4">
                    <h2 class="h5 mb-3"><i class="bi bi-phone"></i> Send your advance payment</h2>

                    <p class="mb-2">Send <strong class="be-price">{{ $peso($advanceCents) }}</strong> via GCash to your rider:</p>
                    <div class="be-gcash-box mb-3">
                        <div class="fw-semibold">{{ $rider?->gcash_name ?: $rider?->name }}</div>
                        <div class="fs-5 fw-bold">{{ $rider?->gcash_number }}</div>
                    </div>
                    <p class="small text-muted">
                        This is {{ $advancePercent }}% of the estimated total of {{ $peso($buyForMe->estimatedTotalCents()) }}.
                        Your rider will confirm it in their GCash account before buying your items.
                    </p>

                    <form method="POST" action="{{ route('buyforme.advance', $buyForMe) }}" enctype="multipart/form-data">
                        @csrf

                        <x-form.input name="gcash_reference_number" label="GCash reference number" :required="true"
                                      maxlength="40" autocomplete="off"
                                      help="8 to 30 letters or numbers. Each reference can only be used once." />

                        <div class="mb-3">
                            <label for="advance_proof" class="form-label fw-semibold">Payment screenshot <span class="text-danger">*</span></label>
                            <input type="file" id="advance_proof" name="advance_proof" required
                                   accept="image/jpeg,image/png,image/webp"
                                   class="form-control @error('advance_proof') is-invalid @enderror">
                            <div class="form-text">JPG, PNG or WEBP, up to 4 MB.</div>
                            @error('advance_proof')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Submit payment details</button>
                    </form>
                </div>
            @elseif ($settlement === \App\Enums\BuyForMeSettlement::AdvanceSubmitted)
                <div class="be-card p-4 mb-4">
                    <h2 class="h5 mb-2"><i class="bi bi-hourglass-split"></i> Waiting for your rider</h2>
                    <p class="mb-2">You sent {{ $peso($advanceCents) }}. Your rider is checking their GCash account.</p>
                    <p class="small text-muted mb-2">Reference: <strong class="text-break">{{ $buyForMe->gcash_reference_number }}</strong></p>
                    @if ($buyForMe->advance_proof)
                        <a href="{{ route('buyforme.advance.proof', $buyForMe) }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-image"></i> View my screenshot
                        </a>
                    @endif
                </div>
            @elseif ($advancePaid)
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="status">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Your advance of {{ $peso($advanceCents) }} was confirmed by the rider.</span>
                </div>
            @endif

            {{-- Request details --}}
            <div class="be-card p-4 mb-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Your request</h2>
                <p class="mb-1 fw-semibold">{{ $buyForMe->establishment_name }}</p>
                <p class="mb-3">{!! nl2br(e($buyForMe->items_summary)) !!}</p>
                <hr>
                <p class="mb-1"><i class="bi bi-geo-alt"></i> {{ $buyForMe->delivery_address }}</p>
                <p class="small text-muted mb-0">Zone: {{ $buyForMe->deliveryZone?->zone_name }}</p>
            </div>

            {{-- Activity --}}
            @if ($buyForMe->statusHistories->isNotEmpty())
                <div class="be-card p-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Activity</h2>
                    <ul class="list-unstyled mb-0">
                        @foreach ($buyForMe->statusHistories->reverse() as $history)
                            <li class="d-flex justify-content-between gap-3 py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                                <span class="fw-semibold">{{ $history->status }}</span>
                                <span class="text-muted small">{{ $history->created_at->format('M j, g:i A') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-5">

            {{-- Rider --}}
            <div class="be-card p-4 mb-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Your rider</h2>
                @if ($rider)
                    <div class="d-flex align-items-center gap-3">
                        <span class="be-avatar">{{ $rider->initials }}</span>
                        <div>
                            <div class="fw-semibold">{{ $rider->name }}</div>
                            @if ($rider->phone)
                                <a href="tel:{{ $rider->phone }}" class="small"><i class="bi bi-telephone"></i> {{ $rider->phone }}</a>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0">No rider yet. Riders in {{ $buyForMe->deliveryZone?->zone_name }} can see your request.</p>
                @endif
            </div>

            {{-- Money --}}
            <div class="be-card p-4 mb-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Cost &amp; payment</h2>

                @if (! $hasEstimate)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery fee</span><strong>{{ $peso($buyForMe->feeCents()) }}</strong>
                    </div>
                    <p class="small text-muted mb-0">The rider will confirm the estimated cost of your items after accepting.</p>
                @else
                    <div class="d-flex justify-content-between mb-2">
                        <span>Estimated items</span><strong>{{ $peso((int) round(((float) $buyForMe->estimated_expense) * 100)) }}</strong>
                    </div>
                    @if ($hasActual)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Actual items</span><strong>{{ $peso((int) round(((float) $buyForMe->actual_expense) * 100)) }}</strong>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery fee</span><strong>{{ $peso($buyForMe->feeCents()) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pt-2 mb-2 border-top">
                        <strong>{{ $hasActual ? 'Final total' : 'Estimated total' }}</strong>
                        <strong class="be-price">{{ $peso($buyForMe->finalTotalCents() ?? $buyForMe->estimatedTotalCents()) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Advance ({{ $advancePercent }}%)</span>
                        <span>
                            <strong>{{ $peso($advanceCents) }}</strong>
                            <span class="be-badge {{ $settlement->badgeClass() }} ms-1">{{ $settlement->label() }}</span>
                        </span>
                    </div>

                    @if ($advancePaid && $remaining !== null)
                        <div class="d-flex justify-content-between pt-2 border-top">
                            @if ($status === \App\Enums\BuyForMeStatus::Delivered)
                                <strong>Settled</strong><strong class="text-success">Paid in full</strong>
                            @elseif ($hasActual && $remaining < 0)
                                <strong>Change to be returned</strong><strong class="text-success">{{ $peso(abs($remaining)) }}</strong>
                            @elseif ($hasActual && $remaining === 0)
                                <strong>Remaining balance</strong><strong class="text-success">Nothing more to pay</strong>
                            @elseif ($hasActual)
                                <strong>Pay the rider on delivery</strong><strong class="be-price">{{ $peso($remaining) }}</strong>
                            @else
                                <strong>Estimated balance at delivery</strong><strong>{{ $peso(max(0, $remaining)) }}</strong>
                            @endif
                        </div>
                        @if (! $hasActual)
                            <p class="small text-muted mt-2 mb-0">
                                The final amount depends on what the rider actually spends, up to {{ $overrunPercent }}% above the estimate.
                            </p>
                        @endif
                    @endif
                @endif

                @if ($buyForMe->receipt_image)
                    <a href="{{ route('buyforme.receipt', $buyForMe) }}" target="_blank" rel="noopener"
                       class="btn btn-outline-primary btn-sm w-100 mt-3">
                        <i class="bi bi-receipt"></i> View purchase receipt
                    </a>
                @endif
            </div>

            {{-- Cancel --}}
            @can('cancel', $buyForMe)
                <form method="POST" action="{{ route('buyforme.cancel', $buyForMe) }}"
                      data-confirm="Cancel this request?">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-x-circle me-1"></i> Cancel request
                    </button>
                </form>
                <p class="small text-muted mt-2">You can cancel until you send the advance payment.</p>
            @elseif (! $status->isFinal())
                <p class="small text-muted">
                    Because the advance payment was sent, this request can’t be cancelled in the app.
                    Please contact the BinalbaganEats team if you need help.
                </p>
            @endcan
        </div>
    </div>
</div>
@endsection

@unless ($status->isFinal())
    @push('scripts')
        <script>
            // Reload every 30 seconds, but only while the tab is visible and nothing is being typed.
            setTimeout(function reload() {
                var typing = document.activeElement && ['INPUT', 'TEXTAREA', 'SELECT'].indexOf(document.activeElement.tagName) !== -1;
                if (document.hidden || typing) { setTimeout(reload, 5000); return; }
                window.location.reload();
            }, 30000);
        </script>
    @endpush
@endunless