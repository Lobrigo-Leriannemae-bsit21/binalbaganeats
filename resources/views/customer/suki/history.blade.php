@extends('layouts.app')

@section('title', 'Suki Points history')

@section('content')
<div class="container py-4 py-lg-5">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('suki.index') }}">Suki Points</a></li>
            <li class="breadcrumb-item active" aria-current="page">History</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Points history</h1>
        <span class="be-badge be-badge-success fs-6">Balance: {{ number_format($balance) }} points</span>
    </div>

    <form method="GET" action="{{ route('suki.history') }}" class="be-card p-3 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-sm-5">
                <label for="type" class="form-label small fw-semibold mb-1">Type</label>
                <select name="type" id="type" class="form-select">
                    <option value="">All</option>
                    <option value="earned" @selected($type === 'earned')>Earned</option>
                    <option value="redeemed" @selected($type === 'redeemed')>Redeemed</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="status" class="form-label small fw-semibold mb-1">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All</option>
                    <option value="pending" @selected($status === 'pending')>Pending / on hold</option>
                    <option value="approved" @selected($status === 'approved')>Available</option>
                    <option value="applied" @selected($status === 'applied')>Used</option>
                </select>
            </div>
            <div class="col-sm-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                @if ($type || $status)
                    <a href="{{ route('suki.history') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </div>
        </div>
    </form>

    @if ($transactions->isEmpty())
        <x-empty-state icon="bi-clock-history" title="No transactions found"
                       text="{{ ($type || $status) ? 'Try different filters.' : 'Your points activity will appear here.' }}" />
    @else
        <div class="be-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Order</th>
                            <th scope="col">Type</th>
                            <th scope="col" class="text-end">Points</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td class="text-nowrap">{{ $transaction->created_at->format('M j, Y g:i A') }}</td>
                                <td>
                                    @if ($transaction->order)
                                        <a href="{{ route('orders.show', $transaction->order) }}">{{ $transaction->order->order_number }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->type->label() }}</td>
                                <td class="text-end {{ $transaction->signedPoints() >= 0 ? 'be-point-plus' : 'be-point-minus' }}">
                                    {{ $transaction->signedPoints() >= 0 ? '+' : '−' }}{{ number_format(abs($transaction->signedPoints())) }}
                                </td>
                                <td><span class="be-badge {{ $transaction->badgeClass() }}">{{ $transaction->statusLabel() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection