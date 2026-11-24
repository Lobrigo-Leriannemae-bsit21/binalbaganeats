@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Vendor Management')
@section('page-title', 'Local Food Vendor Management')
@section('page-subtitle', 'Participating vendors in Barangay San Pedro and approval statuses')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Storefront Name</th>
                    <th>Owner</th>
                    <th>Location & Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vendors as $v)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $v->storefront_name ?? $v->name }}</td>
                        <td>{{ $v->name }}<br><small class="text-be-muted">{{ $v->email }}</small></td>
                        <td class="small">{{ $v->address }}<br><span class="text-be-muted">{{ $v->phone }}</span></td>
                        <td><x-badge :status="ucfirst($v->status)" /></td>
                        <td>
                            @if ($v->status !== 'approved')
                                <form method="POST" action="{{ route('admin.approvals.approve', $v->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                            @else
                                <span class="small text-be-muted">Active Participating Vendor</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection