@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Buy-for-Me Requests')
@section('page-title', 'Buy-for-Me Errand Requests (UC-12)')
@section('page-subtitle', 'Fulfill orders from unregistered establishments using personal funds')

@section('content')
<div class="row g-4">
    @foreach ($errands as $errand)
        <div class="col-md-6">
            <div class="be-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-be-primary">{{ $errand['request_id'] }}</span>
                        <x-badge :status="$errand['status']" />
                    </div>
                    <h6 class="fw-bold text-be-primary mb-1">{{ $errand['establishment'] }}</h6>
                    <p class="small text-be-muted mb-2">Customer: <strong>{{ $errand['customer'] }}</strong> &bull; Deliver to: {{ $errand['delivery_address'] }}</p>
                    <div class="small bg-be-cream p-2 rounded mb-2">Items: {{ $errand['items'] }}</div>
                    <div class="small text-be-muted">Max Budget: <strong>₱{{ number_format($errand['budget'], 2) }}</strong></div>
                    <div class="small text-be-muted">Settlement: {{ $errand['settlement_status'] }}</div>
                </div>

                <div class="border-top pt-3 mt-3">
                    @if (empty($errand['rider']))
                        <form method="POST" action="{{ route('rider.buy-for-me.accept', $errand['request_id']) }}">
                            @csrf
                            <button type="submit" class="btn btn-be-accent w-100">Accept Errand Request</button>
                        </form>
                    @elseif ($errand['rider'] === auth()->user()->name)
                        @if (is_null($errand['actual_expense']))
                            <form method="POST" action="{{ route('rider.buy-for-me.expense', $errand['request_id']) }}" class="input-group">
                                @csrf
                                <input type="number" step="0.01" name="actual_expense" placeholder="Enter actual receipt amount" class="form-control form-control-sm" required>
                                <button type="submit" class="btn btn-sm btn-be-primary">Record Expense</button>
                            </form>
                        @elseif ($errand['settlement_status'] !== 'Settled & Reimbursed')
                            <form method="POST" action="{{ route('rider.buy-for-me.settle', $errand['request_id']) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success w-100">Mark Reimbursement Settled</button>
                            </form>
                        @else
                            <span class="badge bg-success-subtle text-success w-100 py-2">Errand Fully Completed & Settled</span>
                        @endif
                    @else
                        <span class="text-be-muted small fst-italic">Assigned to {{ $errand['rider'] }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection