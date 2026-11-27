<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Password and session security shared by every portal. */
class AccountSecurityService
{
    /** Saves a new password and signs the account out of every other device. */
    public function changePassword(User $user, string $newPassword, Request $request): void
    {
        // The "hashed" cast on User hashes the password. A new remember token invalidates "remember me" cookies.
        $user->forceFill([
            'password' => $newPassword,
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();

        $this->otherSessions($user, $request)->delete();
    }

    /** @return int How many other devices were signed out. */
    public function signOutOtherDevices(User $user, Request $request): int
    {
        $removed = $this->otherSessions($user, $request)->delete();

        $user->forceFill(['remember_token' => Str::random(60)])->save();

        return $removed;
    }

    public function otherDeviceCount(User $user, Request $request): int
    {
        return $this->otherSessions($user, $request)->count();
    }

    private function otherSessions(User $user, Request $request): Builder
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId());
    }
}