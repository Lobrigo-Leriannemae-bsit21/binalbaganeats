<?php

namespace App\Http\Middleware;

use App\Support\RoleHome;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage: ->middleware('role:customer')  or  ->middleware('role:vendor,admin')
 * The check uses the role stored in the database, never anything sent by the browser.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect(RoleHome::loginUrl($request));
        }

        if (! in_array($user->role->value, $roles, true)) {
            // A page for another kind of account. Always blocked; for ordinary page visits we
            // simply bring the person to their own portal instead of showing an error page.
            if ($request->isMethod('GET') && ! $request->expectsJson()) {
                return redirect(RoleHome::url($user))
                    ->with('error', 'That page is not available for your account type, so we brought you to your own portal.');
            }

            abort(403);
        }

        return $next($request);
    }
}