@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Delivery Fee Management')
@section('page-title', 'Delivery Fee & Travel-Time Matrix')
@section('page-subtitle', 'Adjust zone delivery fees and travel-time estimates used in checkout and customer ETA')

@section('content')
<div class="be-card p-4">
    <div class="table-responsive">
        <table class="table be-table mb-0">
            <thead>
                <tr>
                    <th>Zone</th>
                    <th>Distance Range</th>
                    <th>Delivery Fee (₱)</th>
                    <th>Zone Travel Time (Mins)</th>
                    <th>Save Changes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($zones as $zone)
                    <tr>
                        <form method="POST" action="{{ route('admin.fees.update') }}">
                            @csrf
                            <input type="hidden" name="zone_id" value="{{ $zone['id'] }}">
                            <td class="fw-bold text-be-primary">{{ $zone['name'] }}</td>
                            <td class="small text-be-muted">{{ $zone['distance'] }}</td>
                            <td style="width: 160px;">
                                <input type="number" step="0.01" name="fee" value="{{ $zone['fee'] }}" class="form-control form-control-sm" required>
                            </td>
                            <td style="width: 160px;">
                                <input type="number" name="travel_mins" value="{{ $zone['travel_mins'] }}" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-sm btn-be-primary">Update Rate</button>
                            </td>
                        </form>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection