<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    private function getSampleVendors(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Nanay Rosa’s Lutong Bahay',
                'barangay' => 'Brgy. San Pedro',
                'category' => 'Carinderia & Rice Meals',
                'prep_time' => '15–20 mins',
                'status' => 'Available',
                'rating' => '4.9',
                'description' => 'Authentic Negrense home-cooked viands, KBL, and freshly steamed rice along Poblacion Market Road.',
                'menu' => [
                    ['id' => 101, 'name' => 'Kadyos, Baboy, Langka (KBL)', 'price' => 85.00, 'prep' => '15 mins', 'available' => true],
                    ['id' => 102, 'name' => 'Pork Adobo with Egg & Rice', 'price' => 75.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 103, 'name' => 'Sinigang na Bangus Bowl', 'price' => 95.00, 'prep' => '20 mins', 'available' => true],
                ],
            ],
            [
                'id' => 2,
                'name' => 'San Pedro Batchoy & Grill',
                'barangay' => 'Brgy. San Pedro',
                'category' => 'Noodles & Inasal',
                'prep_time' => '20–25 mins',
                'status' => 'Available',
                'rating' => '4.8',
                'description' => 'Hot La Paz-style batchoy, chicken inasal pechopak, and grilled liempo favorites.',
                'menu' => [
                    ['id' => 201, 'name' => 'Super Special Batchoy', 'price' => 90.00, 'prep' => '15 mins', 'available' => true],
                    ['id' => 202, 'name' => 'Chicken Inasal Pecho + Unli Rice', 'price' => 125.00, 'prep' => '25 mins', 'available' => true],
                    ['id' => 203, 'name' => 'Puto Manapla (3 pcs)', 'price' => 30.00, 'prep' => '5 mins', 'available' => true],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Binalbagan Sweet Cravings & Halo-Halo',
                'barangay' => 'Brgy. San Pedro',
                'category' => 'Merienda & Desserts',
                'prep_time' => '10–15 mins',
                'status' => 'Available',
                'rating' => '4.7',
                'description' => 'Refreshing special halo-halo, empanada, and afternoon snacks right in the heart of San Pedro.',
                'menu' => [
                    ['id' => 301, 'name' => 'Overload Special Halo-Halo', 'price' => 80.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 302, 'name' => 'Crispy Chicken Empanada (2 pcs)', 'price' => 55.00, 'prep' => '12 mins', 'available' => true],
                ],
            ],
        ];
    }

    public function index()
    {
        $vendors = $this->getSampleVendors();
        return view('public.landing', compact('vendors'));
    }

    public function vendors(Request $request)
    {
        $vendors = $this->getSampleVendors();
        $search = strtolower($request->query('search', ''));

        if ($search) {
            $vendors = array_filter($vendors, fn($v) =>
                str_contains(strtolower($v['name']), $search) ||
                str_contains(strtolower($v['category']), $search)
            );
        }

        return view('public.vendors', compact('vendors', 'search'));
    }

    public function vendorDetails($id)
    {
        $vendors = $this->getSampleVendors();
        $vendor = collect($vendors)->firstWhere('id', (int) $id) ?? $vendors[0];

        return view('public.vendor-details', compact('vendor'));
    }
}