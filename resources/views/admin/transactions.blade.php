@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Transactions')
@section('page-title', 'Platform Transactions Overview')
@section('page-subtitle', 'Recorded food orders and Buy-for-Me errand records')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Service Type</th>
                    <th>Customer</th>
                    <th>Vendor / Store</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $tx)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $tx['order_number'] }}</td>
                        <td>{{ $tx['type'] }}</td>
                        <td>{{ $tx['customer'] }}</td>
                        <td>{{ $tx['vendor_or_store'] }}</td>
                        <td>{{ $tx['method'] }}</td>
                        <td class="fw-bold">₱{{ number_format($tx['amount'], 2) }}</td>
                        <td><x-badge :status="$tx['status']" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection