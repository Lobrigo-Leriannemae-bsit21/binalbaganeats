<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Every vendor page works on the vendor's own store profile, so one must exist. */
class EnsureVendorHasStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && $user->vendorProfile()->exists(),
            403,
            'Your store profile is missing. Please contact support.'
        );

        return $next($request);
    }
}