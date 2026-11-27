@extends('layouts.app')

@section('title', 'Change password')

@section('content')
<div class="container py-4 py-lg-5">
    <h1 class="h3 mb-4">My account</h1>

    <div class="row g-4">
        <div class="col-lg-3"><x-account-nav /></div>

        <div class="col-lg-9">
            <form method="POST" action="{{ route('account.password.update') }}" class="be-card p-4" novalidate>
                @csrf
                @method('PUT')

                <h2 class="h5 mb-1">Change password</h2>
                <p class="text-muted small mb-4">After you change it, your other devices will be signed out.</p>

                <x-form.input name="current_password" label="Current password" type="password"
                              :required="true" autocomplete="current-password" />

                <x-form.input name="password" label="New password" type="password"
                              :required="true" autocomplete="new-password"
                              help="At least 8 characters, with letters and numbers." />

                <x-form.input name="password_confirmation" label="Confirm new password" type="password"
                              :required="true" autocomplete="new-password" />

                <button type="submit" class="btn btn-primary">Update password</button>
            </form>
        </div>
    </div>
</div>
@endsection