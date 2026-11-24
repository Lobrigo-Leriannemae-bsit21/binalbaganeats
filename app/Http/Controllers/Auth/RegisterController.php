<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $deliveryAreas = [
            'Zone 1 - Brgy. San Pedro & Poblacion Core',
            'Zone 2 - Brgy. Progreso & Marina',
            'Zone 3 - Brgy. Canmoros & Enclaro',
            'Zone 4 - Brgy. Paglaum & Santol',
            'Zone 5 - Outer Barangays (Payao / Bi-ao)',
        ];

        return view('auth.register', compact('deliveryAreas'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:customer,vendor,rider'],
            'storefront_name' => ['required_if:role,vendor', 'nullable', 'string', 'max:255'],
            'preferred_delivery_area' => ['required_if:role,rider', 'nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Customers are approved immediately; Vendors and Riders require Admin approval
        $status = $validated['role'] === 'customer' ? 'approved' : 'pending';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'role' => $validated['role'],
            'status' => $status,
            'storefront_name' => $validated['role'] === 'vendor' ? $validated['storefront_name'] : null,
            'preferred_delivery_area' => $validated['role'] === 'rider' ? $validated['preferred_delivery_area'] : null,
            'password' => Hash::make($validated['password']),
        ]);

        if ($status === 'approved') {
            Auth::login($user);
            return redirect()->route('customer.dashboard')
                ->with('success', 'Registration successful! Welcome to BinalbaganEats.');
        }

        return redirect()->route('login')
            ->with('success', 'Registration submitted! Your ' . $validated['role'] . ' account is pending System Administrator approval.');
    }
}