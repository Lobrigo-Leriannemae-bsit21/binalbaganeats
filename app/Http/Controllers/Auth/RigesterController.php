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
        $rules = [
            'role' => ['required', 'in:customer,vendor,rider'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($request->role === 'rider') {
            $rules['preferred_delivery_area'] = ['required', 'string', 'max:255'];
        }

        if ($request->role === 'vendor') {
            $rules['storefront_name'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        // Business Rule: Customers are approved immediately; Vendors & Riders require Admin approval
        $status = $validated['role'] === 'customer' ? 'approved' : 'pending';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'role' => $validated['role'],
            'status' => $status,
            'preferred_delivery_area' => $validated['preferred_delivery_area'] ?? null,
            'storefront_name' => $validated['storefront_name'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        if ($status === 'approved') {
            Auth::login($user);
            return redirect()->route('customer.dashboard')
                ->with('success', 'Welcome to BinalbaganEats! Your customer account is ready.');
        }

        return redirect()->route('login')
            ->with('success', 'Registration submitted! Your ' . ucfirst($validated['role']) . ' account is pending System Administrator approval.');
    }
}