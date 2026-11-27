<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PasswordUpdateRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function edit(): View
    {
        return view('customer.account.password');
    }

    public function update(PasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // The "hashed" cast on User hashes the password. A new remember token invalidates "remember me" cookies.
        $user->forceFill([
            'password' => $request->validated('password'),
            'remember_token' => Str::random(60),
        ])->save();

        $request->session()->regenerate();

        // Sign the account out of every other device.
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        return redirect()->route('account.password')
            ->with('success', 'Your password was changed. Other devices were signed out.');
    }
}