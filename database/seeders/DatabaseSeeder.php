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

        // 3. Approved Vendor: Nanay Rosa’s Lutong Bahay
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

        // 4. Approved Vendor: EL’s Food & Drink
        User::updateOrCreate(
            ['email' => 'els@binalbaganeats.test'],
            [
                'name' => 'EL’s Native Coffee & Shakes',
                'storefront_name' => 'EL’s Food & Drink',
                'phone' => '09174445555',
                'address' => 'Brgy. San Pedro, Binalbagan',
                'role' => 'vendor',
                'status' => 'approved',
                'password' => Hash::make('password123'),
            ]
        );

        // 5. Approved Vendor: Kysha’s Pastries
        User::updateOrCreate(
            ['email' => 'kyshas@binalbaganeats.test'],
            [
                'name' => 'Kysha’s Bakeshop',
                'storefront_name' => 'Kysha’s Pastries',
                'phone' => '09176667777',
                'address' => 'Brgy. San Pedro, Binalbagan',
                'role' => 'vendor',
                'status' => 'approved',
                'password' => Hash::make('password123'),
            ]
        );

        // 6. Approved Delivery Rider
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

        // 7. Pending Vendor (To test blocked login & admin approval queue)
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