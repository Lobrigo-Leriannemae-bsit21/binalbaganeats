@extends('layouts.vendor')

@section('title', 'Change password')

@section('content')
<h1 class="h3 mb-1">Change password</h1>
<p class="text-muted mb-4">After you change it, your other devices will be signed out.</p>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <form method="POST" action="{{ route('vendor.account.password.update') }}" class="be-card p-4" novalidate>
            @csrf
            @method('PUT')

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
@endsection