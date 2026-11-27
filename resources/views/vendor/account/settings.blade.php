@extends('layouts.vendor')

@section('title', 'Settings')

@section('content')
<h1 class="h3 mb-1">Settings</h1>
<p class="text-muted mb-4">Your account and security.</p>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="be-card p-4 mb-4">
            <h2 class="h5 mb-3">Account overview</h2>
            <ul class="list-unstyled mb-0">
                <li class="d-flex justify-content-between py-2 border-bottom"><span>Owner</span><strong>{{ $user->name }}</strong></li>
                <li class="d-flex justify-content-between py-2 border-bottom"><span>Email</span><strong class="text-break text-end">{{ $user->email }}</strong></li>
                <li class="d-flex justify-content-between py-2 border-bottom"><span>Account type</span><strong>{{ $user->role->label() }}</strong></li>
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span>Status</span>
                    <span class="be-badge {{ $user->status->badgeClass() }}">{{ $user->status->label() }}</span>
                </li>
                <li class="d-flex justify-content-between py-2"><span>Member since</span><strong>{{ $user->created_at->format('F j, Y') }}</strong></li>
            </ul>
        </div>

        <div class="be-card p-4">
            <h2 class="h5 mb-3">Store settings</h2>
            <ul class="list-unstyled mb-3">
                <li class="d-flex justify-content-between py-2 border-bottom">
                    <span>Store</span>
                    <span class="be-badge {{ $store->is_available ? 'be-badge-success' : 'be-badge-danger' }}">{{ $store->is_available ? 'Open' : 'Closed' }}</span>
                </li>
                <li class="d-flex justify-content-between py-2">
                    <span>GCash payments</span>
                    <strong>{{ $store->gcash_number ? 'On' : 'Off (Cash on Delivery only)' }}</strong>
                </li>
            </ul>
            <a href="{{ route('vendor.store.edit') }}" class="btn btn-outline-primary">Manage store settings</a>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="be-card p-4">
            <h2 class="h5 mb-1">Security</h2>
            <p class="text-muted small mb-3">
                You are signed in on this device
                @if ($otherDevices > 0)
                    and <strong>{{ $otherDevices }} other {{ \Illuminate\Support\Str::plural('device', $otherDevices) }}</strong>.
                @else
                    only.
                @endif
            </p>

            <form method="POST" action="{{ route('vendor.account.settings.signout') }}" novalidate>
                @csrf
                <x-form.input name="current_password" label="Confirm your password" type="password"
                              :required="true" autocomplete="current-password"
                              help="Signs you out everywhere except this device." />
                <button type="submit" class="btn btn-outline-danger">Sign out of other devices</button>
            </form>

            <hr>
            <a href="{{ route('vendor.account.password') }}" class="btn btn-outline-primary">Change password</a>
        </div>
    </div>
</div>
@endsection