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
                'description' => 'Authentic Negrense home-cooked viands, KBL, fresh seafood dishes, and steamed rice along Poblacion Market Road.',
                'menu' => [
                    ['id' => 101, 'name' => 'Kadyos, Baboy, Langka (KBL)', 'price' => 85.00, 'prep' => '15 mins', 'available' => true],
                    ['id' => 102, 'name' => 'Pork Adobo with Egg & Rice', 'price' => 75.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 103, 'name' => 'Sinigang na Bangus Bowl', 'price' => 95.00, 'prep' => '20 mins', 'available' => true],
                    ['id' => 104, 'name' => 'Dinuguan with Puto', 'price' => 70.00, 'prep' => '12 mins', 'available' => false],
                    ['id' => 105, 'name' => 'Pancit Canton Guisado', 'price' => 50.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 106, 'name' => 'Escabeche / Sweet & Sour Fish', 'price' => 75.00, 'prep' => '12 mins', 'available' => true],
                    ['id' => 107, 'name' => 'Kinilaw na Isda', 'price' => 75.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 108, 'name' => 'Arroz Caldo / Goto', 'price' => 45.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 109, 'name' => 'Pritong Isda (Fried Fish)', 'price' => 65.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 110, 'name' => 'Ginataang Kalabasa at Sitaw', 'price' => 55.00, 'prep' => '10 mins', 'available' => true],
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
            [
                'id' => 4,
                'name' => 'EL’s Food & Drink',
                'barangay' => 'Brgy. San Pedro',
                'category' => 'Drinks, Shakes & Rice Meals',
                'prep_time' => '8–12 mins',
                'status' => 'Available',
                'rating' => '4.9',
                'description' => 'Best-selling Fresh Buko Shakes, Matcha & Coffee series, Fruit Sodas, Lemonades, and budget rice meals.',
                'menu' => [
                    ['id' => 401, 'name' => 'Iced Matcha', 'price' => 75.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 402, 'name' => 'Strawberry Matcha', 'price' => 85.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 403, 'name' => 'Coconut Matcha', 'price' => 80.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 404, 'name' => 'Biscoff Matcha', 'price' => 90.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 405, 'name' => 'Strawberry Cold Foam Matcha', 'price' => 95.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 406, 'name' => 'Ube Matcha', 'price' => 90.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 407, 'name' => 'Iced Chocolate', 'price' => 80.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 408, 'name' => 'Strawberry Milk', 'price' => 80.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 409, 'name' => 'Chocoberry', 'price' => 85.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 410, 'name' => 'Blueberry Milk', 'price' => 80.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 411, 'name' => 'Ube Taro Milk', 'price' => 80.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 412, 'name' => 'Classic Lemonade (16oz)', 'price' => 55.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 413, 'name' => 'Lemon Yakult (16oz)', 'price' => 70.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 414, 'name' => 'Blueberry Lemonade (16oz)', 'price' => 75.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 415, 'name' => 'Strawberry Lemonade (16oz)', 'price' => 75.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 416, 'name' => 'Rose / Kiwi / Green Apple / Lychee Lemonade (16oz)', 'price' => 75.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 417, 'name' => 'Fresh Buko Shake (Best Seller)', 'price' => 60.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 418, 'name' => 'Mango Graham Shake', 'price' => 65.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 419, 'name' => 'Avocado Graham Shake', 'price' => 65.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 420, 'name' => 'Blueberry / Strawberry Shake', 'price' => 65.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 421, 'name' => 'Seasonal Fruit Shake (Melon / Dragon Fruit / Guyabano / Ube)', 'price' => 65.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 422, 'name' => 'Mango / Ube Halo-Halo', 'price' => 85.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 423, 'name' => 'Mango Sticky Rice', 'price' => 85.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 424, 'name' => 'Fruit Soda (16oz)', 'price' => 50.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 425, 'name' => 'Native Coffee / Americano', 'price' => 55.00, 'prep' => '6 mins', 'available' => true],
                    ['id' => 426, 'name' => 'Iced Latte / Caramel Macchiato / Salted Caramel / Spanish Latte', 'price' => 75.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 427, 'name' => 'Biscoff Coffee / Campfire Latte / French Vanilla / Mocha / Creme Brulee', 'price' => 85.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 428, 'name' => 'Phin Black / Vietnamese Latte (12oz)', 'price' => 65.00, 'prep' => '8 mins', 'available' => true],
                    ['id' => 429, 'name' => 'Coffee / Non-Coffee Frappe', 'price' => 85.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 430, 'name' => 'Siomai Rice (5 pcs)', 'price' => 60.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 431, 'name' => 'Lumpia Rice (4 pcs)', 'price' => 60.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 432, 'name' => 'Hungarian Sausage (1 pc) with Rice', 'price' => 90.00, 'prep' => '12 mins', 'available' => true],
                    ['id' => 433, 'name' => 'Bacon Rice (4 pcs)', 'price' => 90.00, 'prep' => '12 mins', 'available' => true],
                    ['id' => 434, 'name' => 'Shawarma Rice w/ Egg', 'price' => 85.00, 'prep' => '12 mins', 'available' => true],
                    ['id' => 435, 'name' => 'Kimchi Rice w/ Egg & Spam', 'price' => 95.00, 'prep' => '12 mins', 'available' => true],
                    ['id' => 436, 'name' => 'Beefy / Nacho / Bacon Fries', 'price' => 65.00, 'prep' => '10 mins', 'available' => true],
                    ['id' => 437, 'name' => 'French Fries / Potato Chips / Beef Lumpia / Homemade Siomai', 'price' => 45.00, 'prep' => '8 mins', 'available' => true],
                ],
            ],
            [
                'id' => 5,
                'name' => 'Kysha’s Pastries',
                'barangay' => 'Brgy. San Pedro',
                'category' => 'Cakes & Pastries',
                'prep_time' => '5–10 mins',
                'status' => 'Available',
                'rating' => '4.9',
                'description' => 'Homemade moist cakes by the slice or whole pan—Chocolate De Leche, Ube Yema, and Carrot Cake.',
                'menu' => [
                    ['id' => 501, 'name' => 'Moist Chocolate Cake (Slice)', 'price' => 65.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 502, 'name' => 'Chocolate De Leche (Slice)', 'price' => 75.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 503, 'name' => 'Cheesy Chocolate Cake (Slice)', 'price' => 65.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 504, 'name' => 'Carrot Cake w/ Cream Cheese Frosting (Slice)', 'price' => 95.00, 'prep' => '5 mins', 'available' => true],
                    ['id' => 505, 'name' => 'Ube Yema Cake (Slice)', 'price' => 85.00, 'prep' => '5 mins', 'available' => true],
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
                str_contains(strtolower($v['category']), $search) ||
                str_contains(strtolower($v['description']), $search)
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