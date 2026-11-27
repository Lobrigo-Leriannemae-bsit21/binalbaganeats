@extends('layouts.app')

@section('title', 'Create account')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="be-card overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-5 be-auth-side d-none d-md-flex flex-column justify-content-center p-5">
                        <h2 class="h3 mb-3">Join BinalbaganEats</h2>
                        <p class="mb-0">Create a free customer account to discover local food, earn Suki Points, and get your meals delivered.</p>
                    </div>

                    <div class="col-md-7 p-4 p-md-5">
                        <h1 class="h4 mb-1">Create your account</h1>
                        <p class="text-muted mb-4">It only takes a minute.</p>

                        <form method="POST" action="{{ route('register') }}" novalidate>
                            @csrf

                            <x-form.input name="name" label="Full name" :required="true" autocomplete="name" autofocus />

                            <x-form.input name="email" label="Email address" type="email"
                                          :required="true" autocomplete="email" />

                            <x-form.input name="phone" label="Mobile number" type="tel" :required="true"
                                          placeholder="09171234567" autocomplete="tel"
                                          help="Riders use this number to contact you about your delivery." />

                            <x-form.input name="address" label="Delivery address (optional)"
                                          autocomplete="street-address"
                                          help="You can change this later when you check out." />

                            <div class="row">
                                <div class="col-sm-6">
                                    <x-form.input name="password" label="Password" type="password"
                                                  :required="true" autocomplete="new-password"
                                                  help="At least 8 characters, with letters and numbers." />
                                </div>
                                <div class="col-sm-6">
                                    <x-form.input name="password_confirmation" label="Confirm password" type="password"
                                                  :required="true" autocomplete="new-password" />
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-2">Create account</button>
                        </form>

                        <p class="text-center text-muted mt-4 mb-0">
                            Already have an account? <a href="{{ route('login') }}">Log in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection