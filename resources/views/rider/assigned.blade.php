@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Assigned Deliveries')
@section('page-title', 'Assigned Deliveries')
@section('page-subtitle', 'All delivery jobs currently assigned to you')

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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jobs as $job)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $job['order_number'] }}</td>
                        <td>{{ $job['vendor'] }}</td>
                        <td>{{ $job['customer'] }}<br><small class="text-be-muted">{{ $job['address'] }}</small></td>
                        <td class="fw-bold">₱{{ number_format($job['delivery_fee'], 2) }}</td>
                        <td><x-badge :status="$job['status']" /></td>
                        <td>
                            <a href="{{ route('rider.details', $job['order_number']) }}" class="btn btn-sm btn-be-primary">View & Update</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection