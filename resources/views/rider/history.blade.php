@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Delivery History')
@section('page-title', 'Completed Delivery History')
@section('page-subtitle', 'Record of all successfully delivered orders')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Vendor</th>
                    <th>Customer & Address</th>
                    <th>Fee</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobs as $job)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $job['order_number'] }}</td>
                        <td>{{ $job['vendor'] }}</td>
                        <td>{{ $job['customer'] }}<br><small class="text-be-muted">{{ $job['address'] }}</small></td>
                        <td class="fw-bold">₱{{ number_format($job['delivery_fee'], 2) }}</td>
                        <td><x-badge :status="$job['status']" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-be-muted">No completed delivery history recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection