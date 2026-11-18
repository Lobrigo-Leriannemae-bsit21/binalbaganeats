@extends('layouts.portal', ['role' => 'rider'])

@section('title', 'Rider Profile')
@section('page-title', 'Rider Account Profile')
@section('page-subtitle', 'Manage contact info and approved service zone')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="be-card p-4">
            <form method="POST" action="{{ route('rider.profile.update') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" value="{{ $user->email }}" class="form-control bg-light" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mobile Number</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Approved Service Zone</label>
                    <input type="text" name="preferred_delivery_area" value="{{ $user->preferred_delivery_area }}" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-be-primary">Save Profile</button>
            </form>
        </div>
    </div>
</div>
@endsection