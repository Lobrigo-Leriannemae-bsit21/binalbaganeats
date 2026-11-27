<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Support\RoleHome;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs out accounts that are not approved, even mid-session
 * (for example when an admin later rejects or suspends the account).
 */
class EnsureAccountIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status !== UserStatus::Approved) {
            $loginUrl = RoleHome::loginUrl($request);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect($loginUrl)
                ->with('error', 'Your account is not active. Please contact support.');
        }

        return $next($request);
    }
}