<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AddressUpdateRequest;
use App\Http\Requests\Customer\ProfileUpdateRequest;
use App\Models\DeliveryZone;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('customer.account.profile', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Only the validated name and phone are saved.
        $request->user()->update($request->validated());

        return redirect()->route('account.profile')->with('success', 'Your profile was updated.');
    }

    public function address(Request $request): View
    {
        return view('customer.account.address', [
            'user' => $request->user(),
            'zones' => DeliveryZone::query()->active()->orderBy('zone_name')->get(),
        ]);
    }

    public function updateAddress(AddressUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('account.address')->with('success', 'Your delivery address was updated.');
    }
}