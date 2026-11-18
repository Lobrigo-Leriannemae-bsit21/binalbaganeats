@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Delivery & Errand Expenses')
@section('page-title', 'Out-of-Pocket Errand Expense Tracking')
@section('page-subtitle', 'Monitor initial personal funds spent on Buy-for-Me requests pending reimbursement')

@section('content')
<div class="be-stat-card accent p-3 mb-4" style="max-width: 350px;">
    <small class="text-be-muted fw-semibold">Total Out-of-Pocket Expenses Recorded</small>
    <h3 class="fw-bold text-be-accent mb-0 mt-1">₱{{ number_format($totalOut, 2) }}</h3>
</div>

<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Establishment</th>
                    <th>Customer</th>
                    <th>Max Budget</th>
                    <th>Actual Expense</th>
                    <th>Settlement Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($errands as $e)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $e['request_id'] }}</td>
                        <td>{{ $e['establishment'] }}</td>
                        <td>{{ $e['customer'] }}</td>
                        <td>₱{{ number_format($e['budget'], 2) }}</td>
                        <td class="fw-bold text-be-accent">₱{{ number_format($e['actual_expense'], 2) }}</td>
                        <td><x-badge :status="$e['settlement_status']" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection