@extends('layouts.app')

@section('title', 'Account settings')

@section('content')
<div class="container py-4 py-lg-5">
    <h1 class="h3 mb-4">My account</h1>

    <div class="row g-4">
        <div class="col-lg-3"><x-account-nav /></div>

        <div class="col-lg-9">

            <div class="be-card p-4 mb-4">
                <h2 class="h5 mb-3">Account overview</h2>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom"><span>Name</span><strong>{{ $user->name }}</strong></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span>Email</span><strong>{{ $user->email }}</strong></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span>Account type</span><strong>{{ $user->role->label() }}</strong></li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span>Status</span>
                        <span class="be-badge {{ $user->status->badgeClass() }}">{{ $user->status->label() }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-2"><span>Member since</span><strong>{{ $user->created_at->format('F j, Y') }}</strong></li>
                </ul>
            </div>

            <div class="be-card p-4 mb-4">
                <h2 class="h5 mb-1">Security</h2>
                <p class="text-muted small mb-3">
                    You are signed in on this device
                    @if ($otherDevices > 0)
                        and <strong>{{ $otherDevices }} other {{ \Illuminate\Support\Str::plural('device', $otherDevices) }}</strong>.
                    @else
                        only.
                    @endif
                </p>

                <form method="POST" action="{{ route('account.settings.signout') }}" novalidate>
                    @csrf
                    <x-form.input name="current_password" label="Confirm your password" type="password"
                                  :required="true" autocomplete="current-password"
                                  help="Signs you out everywhere except this device." />
                    <button type="submit" class="btn btn-outline-danger">Sign out of other devices</button>
                </form>
            </div>

            <div class="be-card p-4">
                <h2 class="h5 mb-3">Shortcuts</h2>
                <div class="row g-2">
                    @foreach ([
                        ['account.profile', 'bi-person', 'Edit profile'],
                        ['account.address', 'bi-geo-alt', 'Delivery address'],
                        ['account.password', 'bi-shield-lock', 'Change password'],
                        ['notifications.index', 'bi-bell', 'Notifications'],
                        ['help', 'bi-question-circle', 'Help Center'],
                    ] as [$routeName, $icon, $label])
                        <div class="col-sm-6">
                            <a href="{{ route($routeName) }}" class="be-choice text-decoration-none text-dark">
                                <i class="bi {{ $icon }} me-2"></i>{{ $label }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection