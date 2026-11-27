@extends('layouts.app')

@section('title', 'Log in')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="be-card overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-5 be-auth-side d-none d-md-flex flex-column justify-content-center p-5">
                        <h2 class="h3 mb-3">Welcome back!</h2>
                        <p class="mb-0">Log in to order from local vendors, earn Suki Points, and track your deliveries.</p>
                    </div>

                    <div class="col-md-7 p-4 p-md-5">
                        <h1 class="h4 mb-1">Log in</h1>
                        <p class="text-muted mb-4">Enter your details to continue.</p>

                        <form method="POST" action="{{ route('login') }}" novalidate>
                            @csrf

                            <x-form.input name="email" label="Email address" type="email"
                                          :required="true" autocomplete="email" autofocus />

                            <x-form.input name="password" label="Password" type="password"
                                          :required="true" autocomplete="current-password" />

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1"
                                       @checked(old('remember'))>
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Log in</button>
                        </form>

                        <p class="text-center text-muted mt-4 mb-0">
                            Don't have an account? <a href="{{ route('register') }}">Register</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection