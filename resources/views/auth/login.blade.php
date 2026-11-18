@extends('layouts.app')

@section('title', 'Login — BinalbaganEats')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="be-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-be-sage mb-3 overflow-hidden" style="width: 64px; height: 64px;">
                        <img src="{{ asset('images/binalbaganeats-scooter-mark.png') }}" alt="BinalbaganEats Logo" class="be-logo-login">
                    </div>
                    <h3 class="fw-bold text-be-primary mb-1">Welcome Back</h3>
                    <p class="text-be-muted small">Log in to your BinalbaganEats account</p>
                </div>

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="name@example.com" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-be-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </button>
                </form>

                <hr class="my-4">

                <div class="bg-be-sage rounded-3 p-3 small">
                    <div class="fw-bold text-be-primary mb-1">Prototype Quick-Test Accounts (Password: <code>password123</code>)</div>
                    <ul class="mb-0 ps-3 text-be-muted">
                        <li><strong>Customer:</strong> customer@binalbaganeats.test</li>
                        <li><strong>Vendor:</strong> vendor@binalbaganeats.test</li>
                        <li><strong>Rider:</strong> rider@binalbaganeats.test</li>
                        <li><strong>Admin:</strong> admin@binalbaganeats.test</li>
                    </ul>
                </div>

                <p class="text-center small text-be-muted mt-4 mb-0">
                    Don't have an account? <a href="{{ route('register') }}" class="fw-bold text-be-accent text-decoration-none">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection