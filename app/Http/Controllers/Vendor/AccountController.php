<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ConfirmPasswordRequest;
use App\Http\Requests\Customer\PasswordUpdateRequest;
use App\Http\Requests\Customer\ProfileUpdateRequest;
use App\Services\AccountSecurityService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** The vendor's own account (owner name, phone, password, devices). Store details live in StoreController. */
class AccountController extends Controller
{
    public function __construct(private readonly AccountSecurityService $security)
    {
    }

    public function profile(Request $request): View
    {
        return view('vendor.account.profile', ['user' => $request->user()]);
    }

    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {
        // Only the validated name and phone are saved. Email, role and status are never accepted.
        $request->user()->update($request->validated());

        return redirect()->route('vendor.account.profile')->with('success', 'Your profile was updated.');
    }

    public function password(): View
    {
        return view('vendor.account.password');
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $this->security->changePassword($request->user(), $request->validated('password'), $request);

        return redirect()->route('vendor.account.password')
            ->with('success', 'Your password was changed. Other devices were signed out.');
    }

    public function settings(Request $request): View
    {
        return view('vendor.account.settings', [
            'user' => $request->user(),
            'store' => $request->user()->vendorProfile,
            'otherDevices' => $this->security->otherDeviceCount($request->user(), $request),
        ]);
    }

    public function signOutOthers(ConfirmPasswordRequest $request): RedirectResponse
    {
        $removed = $this->security->signOutOtherDevices($request->user(), $request);

        return redirect()->route('vendor.account.settings')->with(
            'success',
            $removed > 0
                ? 'You were signed out of '.$removed.' other '.Str::plural('device', $removed).'.'
                : 'You are not signed in on any other device.'
        );
    }
}