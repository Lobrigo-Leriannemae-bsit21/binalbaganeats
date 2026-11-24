@extends('layouts.portal', ['role' => 'customer'])

@section('title', 'My Orders')
@section('page-title', 'My Orders & History')
@section('page-subtitle', 'View your active and past orders and submit GCash payment details')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Vendor & Items</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th>Order Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $order['order_number'] }}<br><small class="text-be-muted fw-normal">{{ $order['created_at'] }}</small></td>
                        <td>
                            <div class="fw-semibold">{{ $order['vendor'] }}</div>
                            <small class="text-be-muted">{{ $order['items'] }}</small>
                        </td>
                        <td>
                            <div>{{ $order['payment_method'] }}</div>
                            <x-badge :status="$order['payment_status']" />
                            @if ($order['gcash_ref'])
                                <div class="small text-be-muted mt-1">Ref: {{ $order['gcash_ref'] }}</div>
                            @endif
                        </td>
                        <td class="fw-bold">₱{{ number_format($order['total'], 2) }}</td>
                        <td><x-badge :status="$order['status']" /></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('customer.tracking', $order['order_number']) }}" class="btn btn-sm btn-be-primary">Track & ETA</a>
                                @if ($order['payment_method'] === 'GCash' && in_array($order['payment_status'], ['Pending Payment', 'Awaiting Verification']))
                                    <button type="button" class="btn btn-sm btn-be-accent" data-bs-toggle="modal" data-bs-target="#gcashModal{{ $loop->index }}">
                                        Submit GCash
                                    </button>
                                @endif
                            </div>

                            {{-- UC-04.1 GCash Payment Submission Modal --}}
                            <x-modal id="gcashModal{{ $loop->index }}" title="Submit GCash Reference — {{ $order['order_number'] }}">
                                <form method="POST" action="{{ route('customer.orders.gcash', $order['order_number']) }}" enctype="multipart/form-data">
                                    @csrf
                                    <p class="small text-be-muted">Complete your GCash transfer externally, then enter the transaction reference number below for manual vendor verification.</p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">GCash Reference Number <span class="text-danger">*</span></label>
                                        <input type="text" name="gcash_ref" value="{{ $order['gcash_ref'] }}" class="form-control" placeholder="e.g., 90123487123" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Upload GCash Receipt Screenshot (Optional)</label>
                                        <input type="file" name="gcash_proof" class="form-control" accept="image/*">
                                    </div>
                                    <button type="submit" class="btn btn-be-accent w-100">Submit Payment Details</button>
                                </form>
                            </x-modal>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection