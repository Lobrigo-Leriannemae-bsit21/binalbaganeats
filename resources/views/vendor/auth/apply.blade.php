@extends('layouts.portal-auth')

@section('title', 'Apply as a vendor')
@section('body_class', 'role-vendor')
@section('card_class', 'wide')

@section('content')
    <div class="text-center mb-4">
        <div class="d-flex justify-content-center mb-2">
            <x-logo :size="52" :tagline="false" :href="route('home')" />
        </div>
        <div class="be-portal-label">Vendor application</div>
        <p class="text-muted mb-0">Tell us about you and your store. We review every application before it goes live.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">Please check the highlighted fields and try again.</div>
    @endif

    <form method="POST" action="{{ route('vendor.apply.store') }}" novalidate>
        @csrf

        <h2 class="h6 text-uppercase text-muted mb-3">About you</h2>
        <div class="row">
            <div class="col-md-6">
                <x-form.input name="name" label="Owner’s full name" :required="true" autocomplete="name" />
            </div>
            <div class="col-md-6">
                <x-form.input name="phone" label="Mobile number" type="tel" :required="true"
                              placeholder="09171234567" autocomplete="tel" />
            </div>
        </div>

        <x-form.input name="email" label="Email address" type="email" :required="true" autocomplete="email"
                      help="You will use this to sign in." />

        <div class="row">
            <div class="col-md-6">
                <x-form.input name="password" label="Password" type="password" :required="true"
                              autocomplete="new-password" help="At least 8 characters, with letters and numbers." />
            </div>
            <div class="col-md-6">
                <x-form.input name="password_confirmation" label="Confirm password" type="password"
                              :required="true" autocomplete="new-password" />
            </div>
        </div>

        <h2 class="h6 text-uppercase text-muted mt-3 mb-3">Your store</h2>

        <x-form.input name="business_name" label="Store name" :required="true" maxlength="255" />
        <x-form.input name="business_address" label="Store address" :required="true" maxlength="255"
                      placeholder="Street, barangay, Binalbagan" />

        <div class="mb-4">
            <label for="description" class="form-label fw-semibold">Short description</label>
            <textarea id="description" name="description" rows="3" maxlength="1000"
                      class="form-control @error('description') is-invalid @enderror"
                      placeholder="What do you sell?">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit application</button>
    </form>

    <p class="text-center small mt-4 mb-0">
        Already approved? <a href="{{ route('vendor.login') }}">Log in</a>
    </p>
@endsection