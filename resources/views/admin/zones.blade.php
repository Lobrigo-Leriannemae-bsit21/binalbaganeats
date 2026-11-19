@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Delivery Zone Management')
@section('page-title', 'Delivery Zone Management (UC-14)')
@section('page-subtitle', 'Define geographical service zones across Binalbagan without live GPS dependencies')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="be-card p-4">
            <h6 class="fw-bold text-be-primary mb-3">Add / Update Delivery Zone</h6>
            <form method="POST" action="{{ route('admin.zones.save') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Zone ID (Leave blank for new)</label>
                    <input type="text" name="id" class="form-control" placeholder="e.g., Z1">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Zone Name & Barangays</label>
                    <input type="text" name="name" class="form-control" placeholder="Zone 6 - Brgy. Bi-ao" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Estimated Distance Range</label>
                    <input type="text" name="distance" class="form-control" placeholder="14.1 – 18.0 km" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Delivery Fee (₱)</label>
                        <input type="number" step="0.01" name="fee" value="40" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Travel Time (Mins)</label>
                        <input type="number" name="travel_mins" value="20" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-be-accent w-100">Save Delivery Zone</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="be-card p-4">
            <h6 class="fw-bold text-be-primary mb-3">Configured Binalbagan Delivery Zones</h6>
            <div class="table-responsive">
                <table class="table be-table mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Zone Name</th>
                            <th>Distance Range</th>
                            <th>Base Fee</th>
                            <th>Travel Est.</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($zones as $zone)
                            <tr>
                                <td class="fw-bold text-be-primary">{{ $zone['id'] }}</td>
                                <td class="fw-semibold">{{ $zone['name'] }}</td>
                                <td class="small text-be-muted">{{ $zone['distance'] }}</td>
                                <td class="fw-bold text-be-accent">₱{{ number_format($zone['fee'], 2) }}</td>
                                <td>{{ $zone['travel_mins'] }} mins</td>
                                <td><x-badge :status="$zone['status']" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection