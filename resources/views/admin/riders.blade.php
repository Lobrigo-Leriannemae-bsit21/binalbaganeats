@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Rider Management')
@section('page-title', 'Delivery Rider & Service Area Management')
@section('page-subtitle', 'Manage registered delivery riders and their administrator-approved delivery zones')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Rider Name</th>
                    <th>Contact</th>
                    <th>Assigned Delivery Zone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($riders as $r)
                    <tr>
                        <td class="fw-bold text-be-primary">{{ $r->name }}<br><small class="text-be-muted">{{ $r->email }}</small></td>
                        <td class="small">{{ $r->phone }}<br><span class="text-be-muted">{{ $r->address }}</span></td>
                        <td class="fw-semibold">{{ $r->preferred_delivery_area ?? 'Unassigned' }}</td>
                        <td><x-badge :status="ucfirst($r->status)" /></td>
                        <td>
                            @if ($r->status !== 'approved')
                                <form method="POST" action="{{ route('admin.approvals.approve', $r->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve Rider</button>
                                </form>
                            @else
                                <span class="small text-success fw-semibold">Eligible for Dispatch</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection