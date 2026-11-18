@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'Suki Points')
@section('page-title', 'Suki Points Loyalty Rewards')
@section('page-subtitle', 'Earn points on completed orders and redeem vendor-recognized discount credits')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="be-card p-4 h-100">
            <small class="text-be-muted fw-semibold">Available Suki Points</small>
            <h2 class="fw-extrabold text-be-primary my-1">{{ $points }} Points</h2>
            <p class="small text-be-muted mb-0">Points have no cash value and are earned automatically from completed food orders.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="be-card p-4 h-100 bg-be-sage border-0">
            <small class="text-be-primary fw-semibold">Active Redeemed Discount Credit</small>
            <h2 class="fw-extrabold text-be-accent my-1">₱{{ number_format($credit, 2) }}</h2>
            <p class="small text-be-muted mb-0">Ready to apply on your next food checkout.</p>
        </div>
    </div>
</div>

<div class="be-card p-4 mb-4">
    <h5 class="fw-bold text-be-primary mb-3">Redeem Suki Points</h5>
    <div class="row g-3">
        @foreach ([['pts' => 50, 'val' => 15], ['pts' => 100, 'val' => 35], ['pts' => 200, 'val' => 80]] as $tier)
            <div class="col-md-4">
                <div class="border rounded-3 p-3 text-center bg-be-cream">
                    <span class="badge bg-be-primary mb-2">{{ $tier['pts'] }} Suki Points</span>
                    <h4 class="fw-bold text-be-accent">₱{{ $tier['val'] }}.00 Off</h4>
                    <p class="small text-be-muted">Vendor-recognized discount credit</p>
                    <form method="POST" action="{{ route('customer.suki-points.redeem') }}">
                        @csrf
                        <input type="hidden" name="tier" value="{{ $tier['pts'] }}">
                        <button type="submit" class="btn btn-sm btn-be-primary w-100" {{ $points < $tier['pts'] ? 'disabled' : '' }}>
                            {{ $points >= $tier['pts'] ? 'Redeem Reward' : 'Insufficient Points' }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="be-card p-4">
    <h6 class="fw-bold text-be-primary mb-3">Suki Points Ledger</h6>
    <table class="table be-table mb-0">
        <thead>
            <tr>
                <th>Date</th>
                <th>Activity</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ledger as $entry)
                <tr>
                    <td class="small text-be-muted">{{ $entry['date'] }}</td>
                    <td>{{ $entry['activity'] }}</td>
                    <td class="fw-bold {{ $entry['type'] === 'earn' ? 'text-success' : 'text-be-accent' }}">{{ $entry['points'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection