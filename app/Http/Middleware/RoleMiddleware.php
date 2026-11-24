<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        $user = Auth::user();

        // Enforce approval rules for vendor and rider accounts
        if (in_array($user->role, ['vendor', 'rider']) && $user->status !== 'approved') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $msg = $user->status === 'rejected'
                ? 'Your registration was rejected by the System Administrator. Access is unavailable.'
                : 'Your account is still pending administrator approval. Access is unavailable until approved.';

            return redirect()->route('login')->with('error', $msg);
        }

        // Prevent cross-role URL access
        if ($user->role !== $role) {
            return redirect()->route($user->role . '.dashboard')
                ->with('error', 'Unauthorized access: You have been redirected to your assigned portal.');
        }

        return $next($request);
    }
}