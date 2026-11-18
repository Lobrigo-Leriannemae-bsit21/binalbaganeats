@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Completed Orders')
@section('page-title', 'Completed & Closed Orders')
@section('page-subtitle', 'History of delivered and declined customer orders')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Food Subtotal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $order['order_number'] }}</td>
                        <td class="small text-be-muted">{{ $order['created_at'] }}</td>
                        <td>{{ $order['customer'] }}</td>
                        <td class="small">{{ $order['items'] }}</td>
                        <td class="fw-bold">₱{{ number_format($order['subtotal'], 2) }}</td>
                        <td><x-badge :status="$order['status']" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection