<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('vendor.auth.login');
    }

    /** Only approved VENDOR accounts can sign in here. */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(UserRole::Vendor);

        $request->session()->regenerate();

        return redirect()->intended(route('vendor.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login')->with('status', 'You have been logged out.');
    }
}