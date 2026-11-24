<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    private function initializeVendorSession(): void
    {
        $email = Auth::user()->email ?? 'vendor@binalbaganeats.test';

        // Reset session if logged-in vendor changed
        if (session('vendor_owner_email') !== $email) {
            session()->forget(['vendor_storefront', 'vendor_menu', 'vendor_orders']);
        }

        if (!session()->has('vendor_storefront')) {
            if ($email === 'els@binalbaganeats.test') {
                $storefront = [
                    'name' => 'EL’s Food & Drink',
                    'barangay' => 'Brgy. San Pedro, Binalbagan',
                    'address' => Auth::user()->address ?? 'Brgy. San Pedro, Binalbagan',
                    'category' => 'Drinks, Shakes & Rice Meals',
                    'prep_time' => 10,
                    'status' => 'Available',
                    'operating_hours' => '8:00 AM – 8:00 PM',
                    'gcash_number' => '0917-444-5555',
                    'gcash_name' => 'EL’s Native Coffee & Shakes',
                    'description' => 'Fresh Buko Shakes, Matcha & Coffee series, Fruit Sodas, Lemonades, and budget rice meals.',
                ];

                $menu = [
                    // Matcha Series
                    ['id' => 401, 'name' => 'Iced Matcha', 'category' => 'Matcha Series', 'price' => 75.00, 'prep' => 8, 'available' => true, 'desc' => 'Classic iced green tea latte.'],
                    ['id' => 402, 'name' => 'Strawberry Matcha', 'category' => 'Matcha Series', 'price' => 85.00, 'prep' => 8, 'available' => true, 'desc' => 'Layered strawberry puree and iced matcha.'],
                    ['id' => 403, 'name' => 'Coconut Matcha', 'category' => 'Matcha Series', 'price' => 80.00, 'prep' => 8, 'available' => true, 'desc' => 'Refreshing coconut milk blended with iced matcha.'],
                    ['id' => 404, 'name' => 'Biscoff Matcha', 'category' => 'Matcha Series', 'price' => 90.00, 'prep' => 10, 'available' => true, 'desc' => 'Iced matcha topped with crushed Lotus Biscoff biscuits.'],
                    ['id' => 405, 'name' => 'Strawberry Cold Foam Matcha', 'category' => 'Matcha Series', 'price' => 95.00, 'prep' => 10, 'available' => true, 'desc' => 'Strawberry matcha finished with velvety cold foam.'],
                    ['id' => 406, 'name' => 'Ube Matcha', 'category' => 'Matcha Series', 'price' => 90.00, 'prep' => 8, 'available' => true, 'desc' => 'Creamy purple yam base layered with iced matcha.'],

                    // Milk Series
                    ['id' => 407, 'name' => 'Iced Chocolate', 'category' => 'Milk Series', 'price' => 80.00, 'prep' => 8, 'available' => true, 'desc' => 'Rich chilled cocoa milk drink.'],
                    ['id' => 408, 'name' => 'Strawberry Milk', 'category' => 'Milk Series', 'price' => 80.00, 'prep' => 8, 'available' => true, 'desc' => 'Sweet strawberry fruit milk.'],
                    ['id' => 409, 'name' => 'Chocoberry', 'category' => 'Milk Series', 'price' => 85.00, 'prep' => 8, 'available' => true, 'desc' => 'Chocolate milk layered with strawberry puree.'],
                    ['id' => 410, 'name' => 'Blueberry Milk', 'category' => 'Milk Series', 'price' => 80.00, 'prep' => 8, 'available' => true, 'desc' => 'Creamy milk swirled with blueberry jam.'],
                    ['id' => 411, 'name' => 'Ube Taro Milk', 'category' => 'Milk Series', 'price' => 80.00, 'prep' => 8, 'available' => true, 'desc' => 'Smooth ube and taro flavored iced milk.'],

                    // Lemonade
                    ['id' => 412, 'name' => 'Classic Lemonade (16oz)', 'category' => 'Lemonade', 'price' => 55.00, 'prep' => 5, 'available' => true, 'desc' => 'Freshly squeezed house lemonade (22oz: ₱70).'],
                    ['id' => 413, 'name' => 'Lemon Yakult (16oz)', 'category' => 'Lemonade', 'price' => 70.00, 'prep' => 5, 'available' => true, 'desc' => 'Fresh lemonade blended with Yakult probiotic drink (22oz: ₱85).'],
                    ['id' => 414, 'name' => 'Blueberry Lemonade (16oz)', 'category' => 'Lemonade', 'price' => 75.00, 'prep' => 5, 'available' => true, 'desc' => 'Fruity blueberry-infused fresh lemonade (22oz: ₱85).'],
                    ['id' => 415, 'name' => 'Strawberry Lemonade (16oz)', 'category' => 'Lemonade', 'price' => 75.00, 'prep' => 5, 'available' => true, 'desc' => 'Sweet strawberry fresh lemonade (22oz: ₱85).'],
                    ['id' => 416, 'name' => 'Rose / Kiwi / Green Apple / Lychee Lemonade (16oz)', 'category' => 'Lemonade', 'price' => 75.00, 'prep' => 5, 'available' => true, 'desc' => 'Choice of Rose, Kiwi, Green Apple, or Lychee lemonade (22oz: ₱85).'],

                    // Fruit Shake & Summer Specials
                    ['id' => 417, 'name' => 'Fresh Buko Shake (Best Seller)', 'category' => 'Fruit Shake', 'price' => 60.00, 'prep' => 8, 'available' => true, 'desc' => 'Best-selling fresh young coconut shake blended with milk and ice.'],
                    ['id' => 418, 'name' => 'Mango Graham Shake', 'category' => 'Fruit Shake', 'price' => 65.00, 'prep' => 8, 'available' => true, 'desc' => 'Sweet ripe mango shake layered with crushed graham crackers.'],
                    ['id' => 419, 'name' => 'Avocado Graham Shake', 'category' => 'Fruit Shake', 'price' => 65.00, 'prep' => 8, 'available' => true, 'desc' => 'Creamy avocado shake topped with crushed graham crackers.'],
                    ['id' => 420, 'name' => 'Blueberry / Strawberry Shake', 'category' => 'Fruit Shake', 'price' => 65.00, 'prep' => 8, 'available' => true, 'desc' => 'Refreshing berry fruit shakes.'],
                    ['id' => 421, 'name' => 'Seasonal Fruit Shake (Melon / Dragon Fruit / Guyabano / Ube)', 'category' => 'Fruit Shake', 'price' => 65.00, 'prep' => 8, 'available' => true, 'desc' => 'Fresh seasonal fruit shake in Melon, Dragon Fruit, Guyabano, or Ube.'],
                    ['id' => 422, 'name' => 'Mango / Ube Halo-Halo', 'category' => 'Summer Specials', 'price' => 85.00, 'prep' => 10, 'available' => true, 'desc' => 'Creamy shaved ice dessert cup in Mango or Ube flavor.'],
                    ['id' => 423, 'name' => 'Mango Sticky Rice', 'category' => 'Summer Specials', 'price' => 85.00, 'prep' => 10, 'available' => true, 'desc' => 'Sweet glutinous rice with coconut cream and ripe mango slices.'],

                    // Fruit Soda & Coffee Based
                    ['id' => 424, 'name' => 'Fruit Soda (16oz)', 'category' => 'Fruit Soda', 'price' => 50.00, 'prep' => 5, 'available' => true, 'desc' => 'Blueberry, Green Apple, Strawberry, Passion Fruit, Peach Mango, Kiwi, Lychee, Cucumber, or Sweet Grapes (22oz: ₱60).'],
                    ['id' => 425, 'name' => 'Native Coffee / Americano', 'category' => 'Coffee Based', 'price' => 55.00, 'prep' => 6, 'available' => true, 'desc' => 'Local brewed native coffee (black or with milk) or iced Americano.'],
                    ['id' => 426, 'name' => 'Iced Latte / Caramel Macchiato / Salted Caramel / Spanish Latte', 'category' => 'Coffee Based', 'price' => 75.00, 'prep' => 8, 'available' => true, 'desc' => 'Chilled espresso-based milk drinks.'],
                    ['id' => 427, 'name' => 'Biscoff Coffee / Campfire Latte / French Vanilla / Mocha / Creme Brulee', 'category' => 'Coffee Based', 'price' => 85.00, 'prep' => 8, 'available' => true, 'desc' => 'Specialty flavored iced coffees.'],
                    ['id' => 428, 'name' => 'Phin Black / Vietnamese Latte (12oz)', 'category' => 'Coffee Based', 'price' => 65.00, 'prep' => 8, 'available' => true, 'desc' => 'Strong Vietnamese-style drip coffee.'],
                    ['id' => 429, 'name' => 'Coffee / Non-Coffee Frappe', 'category' => 'Frappe Series', 'price' => 85.00, 'prep' => 10, 'available' => true, 'desc' => 'Classic, Caramel, Mocha, Blueberry, Strawberry, Biscoff, Matcha, or Chocolate frappe.'],

                    // Rice Meals & Short Orders
                    ['id' => 430, 'name' => 'Siomai Rice (5 pcs)', 'category' => 'Rice Meal', 'price' => 60.00, 'prep' => 10, 'available' => true, 'desc' => 'Steamed rice topped with 5 pcs homemade siomai.'],
                    ['id' => 431, 'name' => 'Lumpia Rice (4 pcs)', 'category' => 'Rice Meal', 'price' => 60.00, 'prep' => 10, 'available' => true, 'desc' => 'Steamed rice served with 4 pcs crispy beef lumpia.'],
                    ['id' => 432, 'name' => 'Hungarian Sausage (1 pc) with Rice', 'category' => 'Rice Meal', 'price' => 90.00, 'prep' => 12, 'available' => true, 'desc' => 'Grilled Hungarian sausage served with steamed rice.'],
                    ['id' => 433, 'name' => 'Bacon Rice (4 pcs)', 'category' => 'Rice Meal', 'price' => 90.00, 'prep' => 12, 'available' => true, 'desc' => 'Crispy bacon strips served over steamed rice.'],
                    ['id' => 434, 'name' => 'Shawarma Rice w/ Egg', 'category' => 'Rice Meal', 'price' => 85.00, 'prep' => 12, 'available' => true, 'desc' => 'Spiced shawarma meat over java rice topped with fried egg.'],
                    ['id' => 435, 'name' => 'Kimchi Rice w/ Egg & Spam', 'category' => 'Rice Meal', 'price' => 95.00, 'prep' => 12, 'available' => true, 'desc' => 'Kimchi fried rice served with fried egg and sliced Spam.'],
                    ['id' => 436, 'name' => 'Beefy / Nacho / Bacon Fries', 'category' => 'Short Order', 'price' => 65.00, 'prep' => 10, 'available' => true, 'desc' => 'Loaded crispy fries with savory toppings.'],
                    ['id' => 437, 'name' => 'French Fries / Potato Chips / Beef Lumpia / Homemade Siomai', 'category' => 'Short Order', 'price' => 45.00, 'prep' => 8, 'available' => true, 'desc' => 'Classic merienda finger food snacks.'],
                ];

                $orders = [
                    [
                        'order_number' => 'BE-2026-1062',
                        'customer' => 'Lerianne Mae Lobrigo',
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'items' => '2x Fresh Buko Shake (Best Seller), 1x Strawberry Matcha',
                        'subtotal' => 205.00,
                        'delivery_fee' => 25.00,
                        'total' => 230.00,
                        'payment_method' => 'GCash',
                        'payment_status' => 'Awaiting Verification',
                        'gcash_ref' => 'GC-908472110',
                        'status' => 'Payment Verification',
                        'flagged' => false,
                        'created_at' => '4 mins ago',
                    ],
                    [
                        'order_number' => 'BE-2026-1059',
                        'customer' => 'Mark Anthony Reyes',
                        'zone' => 'Zone 2 - Brgy. Progreso & Marina',
                        'items' => '1x Shawarma Rice w/ Egg, 1x Classic Lemonade (16oz)',
                        'subtotal' => 140.00,
                        'delivery_fee' => 35.00,
                        'total' => 175.00,
                        'payment_method' => 'Cash on Delivery',
                        'payment_status' => 'Cash on Delivery',
                        'gcash_ref' => null,
                        'status' => 'Pending',
                        'flagged' => false,
                        'created_at' => '9 mins ago',
                    ],
                ];
            } elseif ($email === 'kyshas@binalbaganeats.test') {
                $storefront = [
                    'name' => 'Kysha’s Pastries',
                    'barangay' => 'Brgy. San Pedro, Binalbagan',
                    'address' => Auth::user()->address ?? 'Brgy. San Pedro, Binalbagan',
                    'category' => 'Cakes & Pastries',
                    'prep_time' => 10,
                    'status' => 'Available',
                    'operating_hours' => '9:00 AM – 7:00 PM',
                    'gcash_number' => '0917-666-7777',
                    'gcash_name' => 'Kysha’s Bakeshop',
                    'description' => 'Homemade moist cakes by the slice or whole pan—Chocolate De Leche, Ube Yema, and Carrot Cake.',
                ];

                $menu = [
                    ['id' => 501, 'name' => 'Moist Chocolate Cake (Slice)', 'category' => 'Cakes & Pastries', 'price' => 65.00, 'prep' => 5, 'available' => true, 'desc' => 'Rich moist chocolate cake slice (Whole 8x3 available for ₱650).'],
                    ['id' => 502, 'name' => 'Chocolate De Leche (Slice)', 'category' => 'Cakes & Pastries', 'price' => 75.00, 'prep' => 5, 'available' => true, 'desc' => 'Moist chocolate cake topped with creamy leche flan (Whole 8x3: ₱680).'],
                    ['id' => 503, 'name' => 'Cheesy Chocolate Cake (Slice)', 'category' => 'Cakes & Pastries', 'price' => 65.00, 'prep' => 5, 'available' => true, 'desc' => 'Chocolate cake topped with grated cheddar cheese (Half Size 4x8: ₱400).'],
                    ['id' => 504, 'name' => 'Carrot Cake w/ Cream Cheese Frosting (Slice)', 'category' => 'Cakes & Pastries', 'price' => 95.00, 'prep' => 5, 'available' => true, 'desc' => 'Spiced carrot cake with rich cream cheese frosting (Whole 8x3: ₱850).'],
                    ['id' => 505, 'name' => 'Ube Yema Cake (Slice)', 'category' => 'Cakes & Pastries', 'price' => 85.00, 'prep' => 5, 'available' => true, 'desc' => 'Soft ube chiffon cake with sweet yema custard frosting (Whole 8x3: ₱750).'],
                ];

                $orders = [
                    [
                        'order_number' => 'BE-2026-1064',
                        'customer' => 'Angela Cruz',
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'items' => '2x Chocolate De Leche (Slice), 1x Ube Yema Cake (Slice)',
                        'subtotal' => 235.00,
                        'delivery_fee' => 25.00,
                        'total' => 260.00,
                        'payment_method' => 'GCash',
                        'payment_status' => 'Awaiting Verification',
                        'gcash_ref' => 'GC-908472305',
                        'status' => 'Payment Verification',
                        'flagged' => false,
                        'created_at' => '2 mins ago',
                    ],
                ];
            } else {
                // Default: Nanay Rosa’s Lutong Bahay (with newly added carinderia dishes)
                $storefront = [
                    'name' => Auth::user()->storefront_name ?? 'Nanay Rosa’s Lutong Bahay',
                    'barangay' => 'Brgy. San Pedro, Binalbagan',
                    'address' => Auth::user()->address ?? 'Poblacion Market Road, Brgy. San Pedro',
                    'category' => 'Carinderia & Rice Meals',
                    'prep_time' => 15,
                    'status' => 'Available',
                    'operating_hours' => '7:00 AM – 7:30 PM',
                    'gcash_number' => '0917-333-4444',
                    'gcash_name' => 'Rosa M. (Nanay Rosa Carinderia)',
                    'description' => 'Authentic Negrense home-cooked viands, KBL, fresh seafood dishes, and steamed rice along Poblacion Market Road.',
                ];

                $menu = [
                    ['id' => 101, 'name' => 'Kadyos, Baboy, Langka (KBL)', 'category' => 'Main Viand', 'price' => 85.00, 'prep' => 15, 'available' => true, 'desc' => 'Classic Ilonggo pigeon peas soup with pork hock and jackfruit.'],
                    ['id' => 102, 'name' => 'Pork Adobo with Egg & Rice', 'category' => 'Rice Meal', 'price' => 75.00, 'prep' => 10, 'available' => true, 'desc' => 'Savory braised pork belly served with boiled egg and steamed rice.'],
                    ['id' => 103, 'name' => 'Sinigang na Bangus Bowl', 'category' => 'Main Viand', 'price' => 95.00, 'prep' => 20, 'available' => true, 'desc' => 'Sour tamarind milkfish soup loaded with local vegetables.'],
                    ['id' => 104, 'name' => 'Dinuguan with Puto', 'category' => 'Merienda', 'price' => 70.00, 'prep' => 12, 'available' => false, 'desc' => 'Rich pork stew paired with steamed rice cakes.'],
                    ['id' => 105, 'name' => 'Pancit Canton Guisado', 'category' => 'Merienda', 'price' => 50.00, 'prep' => 10, 'available' => true, 'desc' => 'Stir-fried egg noodles sautéed with crisp vegetables and savory sauce.'],
                    ['id' => 106, 'name' => 'Escabeche / Sweet & Sour Fish', 'category' => 'Main Viand', 'price' => 75.00, 'prep' => 12, 'available' => true, 'desc' => 'Fried fish steaks simmered in tangy sweet-and-sour sauce with carrots, bell peppers, and black beans.'],
                    ['id' => 107, 'name' => 'Kinilaw na Isda', 'category' => 'Main Viand', 'price' => 75.00, 'prep' => 10, 'available' => true, 'desc' => 'Fresh fish cured in vinegar and coconut milk with red onions, ginger, and green/red chilies.'],
                    ['id' => 108, 'name' => 'Arroz Caldo / Goto', 'category' => 'Merienda', 'price' => 45.00, 'prep' => 8, 'available' => true, 'desc' => 'Warm ginger-infused rice porridge topped with tender meat, egg, and toasted garlic.'],
                    ['id' => 109, 'name' => 'Pritong Isda (Fried Fish)', 'category' => 'Main Viand', 'price' => 65.00, 'prep' => 10, 'available' => true, 'desc' => 'Golden-fried marinated fish slices served hot.'],
                    ['id' => 110, 'name' => 'Ginataang Kalabasa at Sitaw', 'category' => 'Main Viand', 'price' => 55.00, 'prep' => 10, 'available' => true, 'desc' => 'Squash, string beans, and eggplant simmered in rich creamy coconut milk.'],
                ];

                $orders = [
                    [
                        'order_number' => 'BE-2026-1055',
                        'customer' => 'Lerianne Mae Lobrigo',
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'items' => '2x Kadyos, Baboy, Langka (KBL), 1x Pork Adobo with Egg & Rice',
                        'subtotal' => 245.00,
                        'delivery_fee' => 25.00,
                        'total' => 270.00,
                        'payment_method' => 'GCash',
                        'payment_status' => 'Awaiting Verification',
                        'gcash_ref' => 'GC-908471992',
                        'status' => 'Payment Verification',
                        'flagged' => false,
                        'created_at' => '3 mins ago',
                    ],
                    [
                        'order_number' => 'BE-2026-1051',
                        'customer' => 'Mark Anthony Reyes',
                        'zone' => 'Zone 2 - Brgy. Progreso & Marina',
                        'items' => '2x Sinigang na Bangus Bowl',
                        'subtotal' => 190.00,
                        'delivery_fee' => 35.00,
                        'total' => 225.00,
                        'payment_method' => 'Cash on Delivery',
                        'payment_status' => 'Cash on Delivery',
                        'gcash_ref' => null,
                        'status' => 'Pending',
                        'flagged' => false,
                        'created_at' => '7 mins ago',
                    ],
                    [
                        'order_number' => 'BE-2026-1042',
                        'customer' => 'Lerianne Mae Lobrigo',
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'items' => '2x Kadyos, Baboy, Langka (KBL)',
                        'subtotal' => 170.00,
                        'delivery_fee' => 25.00,
                        'total' => 195.00,
                        'payment_method' => 'GCash',
                        'payment_status' => 'Verified',
                        'gcash_ref' => 'GC-908471235',
                        'status' => 'Preparing',
                        'flagged' => false,
                        'created_at' => '20 mins ago',
                    ],
                    [
                        'order_number' => 'BE-2026-1018',
                        'customer' => 'Angela Cruz',
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'items' => '3x Pork Adobo with Egg & Rice',
                        'subtotal' => 225.00,
                        'delivery_fee' => 25.00,
                        'total' => 250.00,
                        'payment_method' => 'GCash',
                        'payment_status' => 'Verified',
                        'gcash_ref' => 'GC-908411204',
                        'status' => 'Delivered',
                        'flagged' => false,
                        'created_at' => 'Yesterday, 1:15 PM',
                    ],
                    [
                        'order_number' => 'BE-2026-1011',
                        'customer' => 'Carlo Mendoza',
                        'zone' => 'Zone 3 - Brgy. Canmoros & Enclaro',
                        'items' => '2x Kadyos, Baboy, Langka (KBL), 1x Sinigang na Bangus Bowl',
                        'subtotal' => 265.00,
                        'delivery_fee' => 45.00,
                        'total' => 310.00,
                        'payment_method' => 'Cash on Delivery',
                        'payment_status' => 'Settled',
                        'gcash_ref' => null,
                        'status' => 'Delivered',
                        'flagged' => false,
                        'created_at' => 'Nov 16, 2026',
                    ],
                ];
            }

            session([
                'vendor_owner_email' => $email,
                'vendor_storefront' => $storefront,
                'vendor_menu' => $menu,
                'vendor_orders' => $orders,
            ]);
        }
    }

    public function dashboard()
    {
        $this->initializeVendorSession();
        $orders = session('vendor_orders', []);
        $storefront = session('vendor_storefront');

        $incoming = array_filter($orders, fn($o) => in_array($o['status'], ['Pending', 'Payment Verification']));
        $activePrep = array_filter($orders, fn($o) => in_array($o['status'], ['Accepted', 'Preparing', 'Ready for Pickup']));
        $completed = array_filter($orders, fn($o) => $o['status'] === 'Delivered');
        $totalSales = array_sum(array_column($completed, 'subtotal'));

        return view('vendor.dashboard', [
            'storefront' => $storefront,
            'incomingCount' => count($incoming),
            'activePrepCount' => count($activePrep),
            'completedCount' => count($completed),
            'totalSales' => $totalSales,
            'recentOrders' => array_slice($orders, 0, 4),
        ]);
    }

    public function storefront()
    {
        $this->initializeVendorSession();
        return view('vendor.storefront', ['storefront' => session('vendor_storefront')]);
    }

    public function updateStorefront(Request $request)
    {
        $this->initializeVendorSession();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'prep_time' => 'required|integer|min:5|max:120',
            'status' => 'required|in:Available,Unavailable',
            'operating_hours' => 'required|string|max:100',
            'gcash_number' => 'required|string|max:30',
            'gcash_name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
        ]);

        $storefront = array_merge(session('vendor_storefront'), $validated);
        session(['vendor_storefront' => $storefront]);

        return back()->with('success', 'Storefront profile, availability status, and preparation estimate updated!');
    }

    public function menu()
    {
        $this->initializeVendorSession();
        return view('vendor.menu', ['menu' => session('vendor_menu', [])]);
    }

    public function addMenuItem(Request $request)
    {
        $this->initializeVendorSession();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:1|max:5000',
            'prep' => 'required|integer|min:1|max:120',
            'desc' => 'required|string|max:300',
            'available' => 'nullable|boolean',
        ]);

        $menu = session('vendor_menu', []);
        $menu[] = [
            'id' => rand(600, 999),
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => (float) $validated['price'],
            'prep' => (int) $validated['prep'],
            'available' => $request->boolean('available'),
            'desc' => $validated['desc'],
        ];

        session(['vendor_menu' => $menu]);
        return back()->with('success', $validated['name'] . ' added to your menu.');
    }

    public function updateMenuItem(Request $request, $id)
    {
        $this->initializeVendorSession();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'prep' => 'required|integer|min:1',
            'available' => 'nullable|boolean',
        ]);

        $menu = session('vendor_menu', []);
        foreach ($menu as &$item) {
            if ((int) $item['id'] === (int) $id) {
                $item['name'] = $validated['name'];
                $item['price'] = (float) $validated['price'];
                $item['prep'] = (int) $validated['prep'];
                $item['available'] = $request->boolean('available');
                break;
            }
        }
        unset($item);

        session(['vendor_menu' => $menu]);
        return back()->with('success', 'Menu item updated.');
    }

    public function deleteMenuItem($id)
    {
        $this->initializeVendorSession();
        $menu = array_values(array_filter(session('vendor_menu', []), fn($i) => (int) $i['id'] !== (int) $id));
        session(['vendor_menu' => $menu]);

        return back()->with('success', 'Menu item removed from catalog.');
    }

    public function incomingOrders()
    {
        $this->initializeVendorSession();
        $orders = array_filter(session('vendor_orders', []), fn($o) => in_array($o['status'], ['Pending', 'Payment Verification']));

        return view('vendor.incoming-orders', compact('orders'));
    }

    public function verifyGcash(Request $request, $orderNumber)
    {
        $this->initializeVendorSession();
        $request->validate([
            'verification_action' => 'required|in:verify,unverify,flag',
        ]);

        $orders = session('vendor_orders', []);
        foreach ($orders as &$order) {
            if ($order['order_number'] === $orderNumber) {
                if ($request->verification_action === 'verify') {
                    $order['payment_status'] = 'Verified';
                    $order['status'] = 'Pending';
                    $msg = "GCash payment for Order #{$orderNumber} marked as Verified. Ready to accept.";
                } elseif ($request->verification_action === 'unverify') {
                    $order['payment_status'] = 'Unverified';
                    $msg = "GCash payment for Order #{$orderNumber} marked as Unverified.";
                } else {
                    $order['flagged'] = true;
                    $msg = "Order #{$orderNumber} flagged for administrative attention.";
                }
                break;
            }
        }
        unset($order);

        session(['vendor_orders' => $orders]);
        return back()->with('success', $msg ?? 'Payment verification updated.');
    }

    public function orderDecision(Request $request, $orderNumber)
    {
        $this->initializeVendorSession();
        $request->validate([
            'decision' => 'required|in:accept,decline,flag',
        ]);

        $orders = session('vendor_orders', []);
        foreach ($orders as &$order) {
            if ($order['order_number'] === $orderNumber) {
                // Business Rule UC-09: GCash orders must be verified before preparation
                if ($request->decision === 'accept' && $order['payment_method'] === 'GCash' && $order['payment_status'] !== 'Verified') {
                    return back()->with('error', "Cannot accept Order #{$orderNumber}: Manual GCash payment verification is required before preparation.");
                }

                if ($request->decision === 'accept') {
                    $order['status'] = 'Accepted';
                    $msg = "Order #{$orderNumber} accepted! You can now update its preparation progress.";
                } elseif ($request->decision === 'decline') {
                    $order['status'] = 'Cancelled';
                    $msg = "Order #{$orderNumber} declined.";
                } else {
                    $order['flagged'] = true;
                    $msg = "Order #{$orderNumber} flagged for administrative review.";
                }
                break;
            }
        }
        unset($order);

        session(['vendor_orders' => $orders]);
        return back()->with('success', $msg ?? 'Order updated.');
    }

    public function orders()
    {
        $this->initializeVendorSession();
        $orders = array_filter(session('vendor_orders', []), fn($o) => in_array($o['status'], ['Accepted', 'Preparing', 'Ready for Pickup']));

        return view('vendor.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $orderNumber)
    {
        $this->initializeVendorSession();
        $request->validate([
            'status' => 'required|in:Accepted,Preparing,Ready for Pickup',
        ]);

        $orders = session('vendor_orders', []);
        foreach ($orders as &$order) {
            if ($order['order_number'] === $orderNumber) {
                $order['status'] = $request->status;
                break;
            }
        }
        unset($order);

        session(['vendor_orders' => $orders]);
        return back()->with('success', "Order #{$orderNumber} status updated to {$request->status}.");
    }

    public function completedOrders()
    {
        $this->initializeVendorSession();
        $orders = array_filter(session('vendor_orders', []), fn($o) => in_array($o['status'], ['Delivered', 'Cancelled']));

        return view('vendor.completed-orders', compact('orders'));
    }

    public function sales()
    {
        $this->initializeVendorSession();
        $completed = array_filter(session('vendor_orders', []), fn($o) => $o['status'] === 'Delivered');
        $grossSales = array_sum(array_column($completed, 'subtotal'));

        return view('vendor.sales', compact('completed', 'grossSales'));
    }

    public function earnings()
    {
        $this->initializeVendorSession();
        $completed = array_filter(session('vendor_orders', []), fn($o) => $o['status'] === 'Delivered');
        $gcashTotal = array_sum(array_map(fn($o) => $o['payment_method'] === 'GCash' ? $o['subtotal'] : 0, $completed));
        $codTotal = array_sum(array_map(fn($o) => $o['payment_method'] === 'Cash on Delivery' ? $o['subtotal'] : 0, $completed));

        return view('vendor.earnings', compact('completed', 'gcashTotal', 'codTotal'));
    }

    public function notifications()
    {
        $notifications = [
            ['title' => 'New GCash Order #BE-2026-1055', 'message' => 'Lerianne Mae Lobrigo submitted GCash Reference GC-908471992 (₱270.00). Please verify manually.', 'time' => '3 mins ago', 'unread' => true],
            ['title' => 'New COD Order #BE-2026-1051', 'message' => 'Mark Anthony Reyes placed a Cash on Delivery order for 2x Sinigang na Bangus Bowl.', 'time' => '7 mins ago', 'unread' => true],
            ['title' => 'Rider Dispatched for #BE-2026-1042', 'message' => 'Cyrus Ace Taborite has been assigned to pick up Order #BE-2026-1042.', 'time' => '15 mins ago', 'unread' => false],
        ];

        return view('vendor.notifications', compact('notifications'));
    }

    public function profile()
    {
        return view('vendor.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        $user->update($validated);
        return back()->with('success', 'Vendor owner profile updated.');
    }
}