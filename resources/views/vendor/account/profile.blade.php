@extends('layouts.vendor')

@section('title', 'Profile')

@section('content')
<h1 class="h3 mb-1">Profile</h1>
<p class="text-muted mb-4">Your personal details as the store owner.</p>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="be-card p-4 mb-4 d-flex align-items-center gap-3">
            <span class="be-avatar be-avatar-lg">{{ $user->initials }}</span>
            <div>
                <div class="h5 mb-0">{{ $user->name }}</div>
                <div class="text-muted small">{{ $user->email }}</div>
                <div class="small text-muted">Member since {{ $user->created_at->format('F Y') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('vendor.account.profile.update') }}" class="be-card p-4" novalidate>
            @csrf
            @method('PUT')

            <h2 class="h5 mb-3">Personal information</h2>

            <x-form.input name="name" label="Full name" :value="$user->name" :required="true" autocomplete="name" />

            <div class="mb-3">
                <label for="email_display" class="form-label fw-semibold">Email address</label>
                <input type="email" id="email_display" class="form-control" value="{{ $user->email }}" readonly>
                <div class="form-text">Your email is your login. To change it, please contact support.</div>
            </div>

            <x-form.input name="phone" label="Mobile number" type="tel" :value="$user->phone" :required="true"
                          autocomplete="tel" placeholder="09171234567"
                          help="Customers and riders may use this number to reach you about an order." />

            <button type="submit" class="btn btn-primary">Save changes</button>
        </form>
    </div>
</div>
@endsection