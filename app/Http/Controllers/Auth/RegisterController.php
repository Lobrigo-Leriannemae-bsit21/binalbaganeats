<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /** Public registration creates CUSTOMER accounts only. */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // Only whitelisted, validated fields are used. The password is hashed by the model cast.
        $user = new User($request->safe()->only(['name', 'email', 'phone', 'address', 'password']));

        // Role and status are assigned server-side and never read from input.
        $user->role = UserRole::Customer;
        $user->status = UserStatus::Approved;
        $user->save();

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to BinalbaganEats, '.$user->name.'!');
    }
}