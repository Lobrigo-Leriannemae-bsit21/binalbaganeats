<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/** Where each kind of account belongs. Rider and admin pages are added in later phases. */
final class RoleHome
{
    /** The home page of the signed-in user's own portal (public home if that portal does not exist yet). */
    public static function url(?User $user): string
    {
        if (! $user) {
            return route('home');
        }

        $name = match ($user->role) {
            UserRole::Customer => 'dashboard',
            UserRole::Vendor => 'vendor.dashboard',
            UserRole::Rider => 'rider.dashboard',
            UserRole::Admin => 'admin.dashboard',
        };

        return Route::has($name) ? route($name) : route('home');
    }

    /** The login page that matches the portal a visitor was trying to open. */
    public static function loginUrl(Request $request): string
    {
        return match (true) {
            $request->is('vendor', 'vendor/*') => route('vendor.login'),
            default => route('login'),
        };
    }
}