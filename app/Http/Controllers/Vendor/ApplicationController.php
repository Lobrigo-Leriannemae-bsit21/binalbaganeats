<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorApplicationRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function create(): View
    {
        return view('vendor.auth.apply');
    }

    /**
     * Creates a PENDING vendor account and its store profile. The account cannot sign in
     * until an administrator approves it.
     */
    public function store(VendorApplicationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['business_address'],
                'password' => $data['password'], // hashed by the model cast
            ]);

            // Role and status are fixed here in code. Nothing from the form can change them.
            $user->role = UserRole::Vendor;
            $user->status = UserStatus::Pending;
            $user->storefront_name = $data['business_name'];
            $user->save();

            $user->vendorProfile()->create([
                'business_name' => $data['business_name'],
                'business_address' => $data['business_address'],
                'description' => $data['description'] ?? null,
            ]);
        });

        return redirect()->route('vendor.login')->with(
            'success',
            'Thank you! Your application was received. You will be able to sign in once the BinalbaganEats team approves your store.'
        );
    }
}