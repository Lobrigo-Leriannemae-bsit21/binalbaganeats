@extends('layouts.portal', ['role' => 'admin'])

@section('title', 'Admin Profile')
@section('page-title', 'System Administrator Profile')
@section('page-subtitle', 'Manage administrator display details')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="be-card p-4">
            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Administrator Name</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" value="{{ $user->email }}" class="form-control bg-light" disabled>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Contact Number</label>
                    <input type="text" name="phone" value="{{ $user->phone }}" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-be-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection