@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Pending Approvals')
@section('page-title', 'Approve Accounts & Delivery Areas (UC-13)')
@section('page-subtitle', 'Verify and approve or reject pending Vendor and Delivery Rider registrations')

@section('content')
@forelse ($pendingUsers as $applicant)
    <div class="be-card p-4 mb-3">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h5 class="fw-bold text-be-primary mb-0">{{ $applicant->name }}</h5>
                    <span class="badge bg-be-sage text-be-primary text-capitalize">{{ $applicant->role }}</span>
                    <x-badge status="Pending" />
                </div>
                <p class="small text-be-muted mb-1">Email: {{ $applicant->email }} &bull; Phone: {{ $applicant->phone }}</p>
                <p class="small text-be-muted mb-0">Address: {{ $applicant->address }}</p>
                @if ($applicant->role === 'vendor')
                    <div class="mt-2 small fw-semibold text-be-primary">Storefront Name: {{ $applicant->storefront_name }}</div>
                @elseif ($applicant->role === 'rider')
                    <div class="mt-2 small fw-semibold text-be-accent">Requested Delivery Area: {{ $applicant->preferred_delivery_area }}</div>
                @endif
            </div>

            <div class="col-lg-6 text-lg-end">
                <form method="POST" action="{{ route('admin.approvals.approve', $applicant->id) }}" class="d-inline-flex flex-wrap gap-2 align-items-center">
                    @csrf
                    @if ($applicant->role === 'rider')
                        <select name="approved_area" class="form-select form-select-sm" style="width: 260px;">
                            <option value="{{ $applicant->preferred_delivery_area }}" selected>{{ $applicant->preferred_delivery_area }}</option>
                            <option value="Zone 1 - Brgy. San Pedro & Poblacion Core">Zone 1 - Brgy. San Pedro & Poblacion Core</option>
                            <option value="Zone 2 - Brgy. Progreso & Marina">Zone 2 - Brgy. Progreso & Marina</option>
                            <option value="Zone 3 - Brgy. Canmoros & Enclaro">Zone 3 - Brgy. Canmoros & Enclaro</option>
                        </select>
                    @endif
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-check-circle me-1"></i> Approve Account
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.approvals.reject', $applicant->id) }}" class="d-inline ms-1">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="be-card p-5 text-center">
        <i class="bi bi-person-check fs-1 text-be-primary"></i>
        <h5 class="fw-bold text-be-primary mt-3">No Pending Registrations</h5>
        <p class="text-be-muted mb-0">All vendor and rider applications have been processed.</p>
    </div>
@endforelse
@endsection