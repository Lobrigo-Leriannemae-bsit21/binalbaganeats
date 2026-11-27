<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ConfirmPasswordRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountSettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $otherDevices = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->count();

        return view('customer.account.settings', [
            'user' => $user,
            'otherDevices' => $otherDevices,
        ]);
    }

    public function signOutOthers(ConfirmPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $removed = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        // Also invalidate "remember me" cookies on other devices.
        $user->forceFill(['remember_token' => Str::random(60)])->save();

        return redirect()->route('account.settings')->with(
            'success',
            $removed > 0
                ? 'You were signed out of '.$removed.' other '.Str::plural('device', $removed).'.'
                : 'You are not signed in on any other device.'
        );
    }
}