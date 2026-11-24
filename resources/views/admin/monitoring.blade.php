@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'System Monitoring')
@section('page-title', 'Monitor System Activities (UC-15)')
@section('page-subtitle', 'Administrative oversight of order records, delivery operations, and flagged reports')

@section('content')
<ul class="nav nav-pills mb-4 gap-2">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'orders' ? 'active bg-be-primary' : 'bg-white text-be-primary border' }}" href="{{ route('admin.monitoring', ['view' => 'orders']) }}">All Order & Errand Operations</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'flagged' ? 'active bg-danger' : 'bg-white text-danger border' }}" href="{{ route('admin.monitoring', ['view' => 'flagged']) }}">Flagged Reports ({{ count($flagged) }})</a>
    </li>
</ul>

<div class="be-card p-4">
    <div class="alert alert-light border small text-be-muted mb-3">
        <i class="bi bi-info-circle me-1"></i> Administrative monitoring is for operational oversight only. Payments, refunds, and reimbursements are settled outside the platform.
    </div>

    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Reference #</th>
                    <th>Type</th>
                    <th>Customer</th>
                    <th>Vendor / Establishment</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach (($tab === 'flagged' ? $flagged : $transactions) as $item)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $item['order_number'] }}</td>
                        <td><span class="badge bg-be-sage text-be-primary">{{ $item['type'] }}</span></td>
                        <td>{{ $item['customer'] }}</td>
                        <td>{{ $item['vendor_or_store'] }}</td>
                        <td class="fw-bold">₱{{ number_format($item['amount'], 2) }}</td>
                        <td><x-badge :status="$item['status']" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection