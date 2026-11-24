@extends('layouts.portal', ['role' => 'vendor'])

@section('title', 'Order Management')
@section('page-title', 'Active Order Preparation')
@section('page-subtitle', 'Update preparation milestones for accepted customer orders')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer & Items</th>
                    <th>Current Stage</th>
                    <th>Update Preparation Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $order['order_number'] }}</td>
                        <td>
                            <div class="fw-semibold">{{ $order['customer'] }}</div>
                            <small class="text-be-muted">{{ $order['items'] }}</small>
                        </td>
                        <td><x-badge :status="$order['status']" /></td>
                        <td>
                            <form method="POST" action="{{ route('vendor.orders.status', $order['order_number']) }}" class="d-flex gap-2">
                                @csrf
                                <select name="status" class="form-select form-select-sm" style="width: 190px;">
                                    <option value="Accepted" {{ $order['status'] === 'Accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="Preparing" {{ $order['status'] === 'Preparing' ? 'selected' : '' }}>Preparing</option>
                                    <option value="Ready for Pickup" {{ $order['status'] === 'Ready for Pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-be-accent">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-be-muted">No orders currently in preparation.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection