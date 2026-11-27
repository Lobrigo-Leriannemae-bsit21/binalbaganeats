@extends('layouts.portal-auth')

@section('title', 'Vendor login')
@section('body_class', 'role-vendor')

@section('content')
    <div class="text-center mb-4">
        <div class="d-flex justify-content-center mb-2">
            <x-logo :size="52" :tagline="false" :href="route('home')" />
        </div>
        <div class="be-portal-label">Vendor Portal</div>
        <p class="text-muted mb-0">Sign in to manage your store</p>
    </div>

    <form method="POST" action="{{ route('vendor.login.store') }}" novalidate>
        @csrf

        <x-form.input name="email" label="Email address" type="email"
                      :required="true" autocomplete="email" autofocus />

        <x-form.input name="password" label="Password" type="password"
                      :required="true" autocomplete="current-password" />

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))>
            <label class="form-check-label" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Log in</button>
    </form>

    <div class="text-center mt-4 small">
        <p class="mb-1">New to BinalbaganEats? <a href="{{ route('vendor.apply') }}">Apply as a vendor</a></p>
        <p class="mb-0 text-muted">Ordering food? <a href="{{ route('login') }}">Customer log in</a></p>
    </div>
@endsection