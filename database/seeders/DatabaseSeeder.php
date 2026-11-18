<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. System Administrator
        User::updateOrCreate(
            ['email' => 'admin@binalbaganeats.test'],
            [
                'name' => 'System Administrator',
                'phone' => '09170000001',
                'address' => 'Binalbagan Municipal Hall, Negros Occidental',
                'role' => 'admin',
                'status' => 'approved',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Approved Customer
        User::updateOrCreate(
            ['email' => 'customer@binalbaganeats.test'],
            [
                'name' => 'Lerianne Mae Lobrigo',
                'phone' => '09171112222',
                'address' => 'Zone 2, Brgy. San Pedro, Binalbagan',
                'role' => 'customer',
                'status' => 'approved',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Approved Local Food Vendor
        User::updateOrCreate(
            ['email' => 'vendor@binalbaganeats.test'],
            [
                'name' => 'Nanay Rosa Carinderia',
                'storefront_name' => 'Nanay Rosa’s Lutong Bahay',
                'phone' => '09173334444',
                'address' => 'Poblacion Market Road, Brgy. San Pedro, Binalbagan',
                'role' => 'vendor',
                'status' => 'approved',
                'password' => Hash::make('password123'),
            ]
        );

        // 4. Approved Delivery Rider
        User::updateOrCreate(
            ['email' => 'rider@binalbaganeats.test'],
            [
                'name' => 'Cyrus Ace Taborite',
                'phone' => '09175556666',
                'address' => 'Brgy. Progreso, Binalbagan',
                'role' => 'rider',
                'status' => 'approved',
                'preferred_delivery_area' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                'password' => Hash::make('password123'),
            ]
        );

        // 5. Pending Vendor (To test blocked login & admin approval queue)
        User::updateOrCreate(
            ['email' => 'pending.vendor@binalbaganeats.test'],
            [
                'name' => 'Mang Jose Inasal',
                'storefront_name' => 'Mang Jose Grill & Batchoy',
                'phone' => '09177778888',
                'address' => 'Brgy. San Pedro Highway, Binalbagan',
                'role' => 'vendor',
                'status' => 'pending',
                'password' => Hash::make('password123'),
            ]
        );
    }
}