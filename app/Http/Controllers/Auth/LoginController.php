<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Enforce approval rules for vendor and rider accounts immediately on login
        if (in_array($user->role, ['vendor', 'rider']) && $user->status !== 'approved') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = $user->status === 'rejected'
                ? 'Your registration was rejected by the System Administrator. Access is unavailable.'
                : 'Your account is still pending System Administrator approval. Please wait until approved.';

            return redirect()->route('login')
                ->withInput($request->only('email'))
                ->with('error', $message);
        }

        return redirect()->intended(route($user->role . '.dashboard'))
            ->with('success', "Welcome back, {$user->name}!");
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out safely.');
    }
}