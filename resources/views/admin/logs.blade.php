@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Activity Logs')
@section('page-title', 'System Activity Logs')
@section('page-subtitle', 'Audit trail of authentication approvals, vendor verifications, and zone updates')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Actor</th>
                    <th>Activity Description</th>
                    <th>DFD Module</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td class="small text-be-muted">{{ $log['timestamp'] }}</td>
                        <td class="fw-semibold text-be-primary">{{ $log['actor'] }}</td>
                        <td>{{ $log['action'] }}</td>
                        <td><span class="badge bg-be-sage text-be-primary">{{ $log['module'] }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection