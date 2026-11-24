@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Sales Records')
@section('page-title', 'Basic Sales Records')
@section('page-subtitle', 'Generated from your completed customer orders (UC-10)')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="be-stat-card p-3">
            <small class="text-be-muted fw-semibold">Completed Order Count</small>
            <h3 class="fw-bold text-be-primary mb-0 mt-1">{{ count($completed) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="be-stat-card accent p-3">
            <small class="text-be-muted fw-semibold">Total Gross Food Sales</small>
            <h3 class="fw-bold text-be-accent mb-0 mt-1">₱{{ number_format($grossSales, 2) }}</h3>
        </div>
    </div>
</div>

<div class="be-card p-4">
    <h6 class="fw-bold text-be-primary mb-3">Completed Order Sales Breakdown</h6>
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Food Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($completed as $sale)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $sale['order_number'] }}</td>
                        <td>{{ $sale['created_at'] }}</td>
                        <td>{{ $sale['payment_method'] }}</td>
                        <td><x-badge :status="$sale['payment_status']" /></td>
                        <td class="fw-bold">₱{{ number_format($sale['subtotal'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection