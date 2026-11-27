<?php

use App\Http\Middleware\EnsureAccountIsApproved;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureVendorHasStore;
use App\Support\RoleHome;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Each role keeps its routes in its own file (rider.php and admin.php arrive in later phases).
            Route::middleware('web')->group(base_path('routes/vendor.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'approved' => EnsureAccountIsApproved::class,
            'vendor.has-store' => EnsureVendorHasStore::class,
        ]);

        // Signed-out visitors go to the login page of the portal they were trying to open.
        $middleware->redirectGuestsTo(fn (Request $request) => RoleHome::loginUrl($request));

        // Signed-in users who open a guest page (like /login) go to their own home.
        $middleware->redirectUsersTo(fn (Request $request) => RoleHome::url($request->user()));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();