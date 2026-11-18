<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private function getVendors(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Nanay Rosa’s Lutong Bahay',
                'barangay' => 'Brgy. San Pedro',
                'category' => 'Carinderia & Rice Meals',
                'prep_time' => 15,
                'status' => 'Available',
                'rating' => '4.9',
                'description' => 'Authentic Negrense home-cooked viands, KBL, and freshly steamed rice along Poblacion Market Road.',
                'menu' => [
                    ['id' => 101, 'name' => 'Kadyos, Baboy, Langka (KBL)', 'price' => 85.00, 'prep' => 15, 'available' => true, 'desc' => 'Classic Ilonggo pigeon peas soup with pork hock and jackfruit.'],
                    ['id' => 102, 'name' => 'Pork Adobo with Egg & Rice', 'price' => 75.00, 'prep' => 10, 'available' => true, 'desc' => 'Savory braised pork belly served with boiled egg and steamed rice.'],
                    ['id' => 103, 'name' => 'Sinigang na Bangus Bowl', 'price' => 95.00, 'prep' => 20, 'available' => true, 'desc' => 'Sour tamarind milkfish soup loaded with local vegetables.'],
                    ['id' => 104, 'name' => 'Dinuguan with Puto', 'price' => 70.00, 'prep' => 12, 'available' => false, 'desc' => 'Rich pork stew paired with steamed rice cakes (Currently sold out).'],
                ],
            ],
            [
                'id' => 2,
                'name' => 'San Pedro Batchoy & Grill',
                'barangay' => 'Brgy. San Pedro',
                'category' => 'Noodles & Inasal',
                'prep_time' => 20,
                'status' => 'Available',
                'rating' => '4.8',
                'description' => 'Hot La Paz-style batchoy, chicken inasal pechopak, and grilled liempo favorites.',
                'menu' => [
                    ['id' => 201, 'name' => 'Super Special Batchoy', 'price' => 90.00, 'prep' => 15, 'available' => true, 'desc' => 'Rich pork broth with miki noodles, chicharon, liver, and egg.'],
                    ['id' => 202, 'name' => 'Chicken Inasal Pecho + Unli Rice', 'price' => 125.00, 'prep' => 25, 'available' => true, 'desc' => 'Charcoal-grilled chicken breast marinated in annatto and calamansi.'],
                    ['id' => 203, 'name' => 'Puto Manapla (3 pcs)', 'price' => 30.00, 'prep' => 5, 'available' => true, 'desc' => 'Soft steamed rice cakes wrapped in banana leaves.'],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Binalbagan Sweet Cravings & Halo-Halo',
                'barangay' => 'Brgy. San Pedro',
                'category' => 'Merienda & Desserts',
                'prep_time' => 12,
                'status' => 'Available',
                'rating' => '4.7',
                'description' => 'Refreshing special halo-halo, empanada, and afternoon snacks right in the heart of San Pedro.',
                'menu' => [
                    ['id' => 301, 'name' => 'Overload Special Halo-Halo', 'price' => 80.00, 'prep' => 10, 'available' => true, 'desc' => 'Shaved ice with leche flan, ube halaya, sweet beans, and macapuno.'],
                    ['id' => 302, 'name' => 'Crispy Chicken Empanada (2 pcs)', 'price' => 55.00, 'prep' => 12, 'available' => true, 'desc' => 'Flaky golden pastry stuffed with seasoned chicken and vegetables.'],
                ],
            ],
        ];
    }

    private function getDeliveryZones(): array
    {
        return [
            ['id' => 'Z1', 'name' => 'Zone 1 - Brgy. San Pedro & Poblacion Core', 'fee' => 25.00, 'travel_mins' => 10],
            ['id' => 'Z2', 'name' => 'Zone 2 - Brgy. Progreso & Marina', 'fee' => 35.00, 'travel_mins' => 15],
            ['id' => 'Z3', 'name' => 'Zone 3 - Brgy. Canmoros & Enclaro', 'fee' => 45.00, 'travel_mins' => 20],
            ['id' => 'Z4', 'name' => 'Zone 4 - Brgy. Paglaum & Santol', 'fee' => 55.00, 'travel_mins' => 25],
            ['id' => 'Z5', 'name' => 'Zone 5 - Outer Barangays (Payao / Bi-ao)', 'fee' => 70.00, 'travel_mins' => 35],
        ];
    }

    private function initializePrototypeSession(): void
    {
        if (!session()->has('customer_orders')) {
            session([
                'customer_orders' => [
                    [
                        'order_number' => 'BE-2026-1042',
                        'type' => 'Food Order',
                        'vendor' => 'Nanay Rosa’s Lutong Bahay',
                        'items' => '2x Kadyos, Baboy, Langka (KBL), 1x Pork Adobo with Egg & Rice',
                        'subtotal' => 245.00,
                        'delivery_fee' => 25.00,
                        'discount' => 0.00,
                        'total' => 270.00,
                        'payment_method' => 'GCash',
                        'payment_status' => 'Verified',
                        'gcash_ref' => 'GC-908471235',
                        'status' => 'Preparing',
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'prep_mins' => 15,
                        'travel_mins' => 10,
                        'dispatch_buffer' => 5,
                        'rider' => 'Cyrus Ace Taborite',
                        'created_at' => 'Today, 11:45 AM',
                    ],
                    [
                        'order_number' => 'BE-2026-1019',
                        'type' => 'Food Order',
                        'vendor' => 'San Pedro Batchoy & Grill',
                        'items' => '1x Super Special Batchoy, 1x Puto Manapla (3 pcs)',
                        'subtotal' => 120.00,
                        'delivery_fee' => 25.00,
                        'discount' => 15.00,
                        'total' => 130.00,
                        'payment_method' => 'Cash on Delivery',
                        'payment_status' => 'Settled',
                        'gcash_ref' => null,
                        'status' => 'Delivered',
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'prep_mins' => 15,
                        'travel_mins' => 10,
                        'dispatch_buffer' => 5,
                        'rider' => 'Cyrus Ace Taborite',
                        'created_at' => 'Yesterday, 6:15 PM',
                    ],
                ],
                'suki_points' => 145,
                'suki_credit' => 15.00,
                'suki_ledger' => [
                    ['date' => 'Nov 18, 2026', 'activity' => 'Earned from Order #BE-2026-1042', 'points' => '+25 pts', 'type' => 'earn'],
                    ['date' => 'Nov 17, 2026', 'activity' => 'Redeemed ₱15.00 Suki Discount Voucher', 'points' => '-50 pts', 'type' => 'redeem'],
                    ['date' => 'Nov 17, 2026', 'activity' => 'Earned from Order #BE-2026-1019', 'points' => '+15 pts', 'type' => 'earn'],
                    ['date' => 'Nov 14, 2026', 'activity' => 'Welcome Suki Loyalty Bonus', 'points' => '+155 pts', 'type' => 'earn'],
                ],
                'buy_for_me_requests' => [
                    [
                        'request_id' => 'BFM-2026-088',
                        'establishment' => 'Binalbagan Public Market Fruit Stand #4',
                        'items' => '2 kg Lacatan Bananas, 1 kg Sweet Mangoes',
                        'budget' => 300.00,
                        'actual_expense' => 265.00,
                        'zone' => 'Zone 1 - Brgy. San Pedro & Poblacion Core',
                        'delivery_address' => 'Zone 2, Brgy. San Pedro, Binalbagan',
                        'rider' => 'Cyrus Ace Taborite',
                        'status' => 'Picked Up',
                        'settlement_status' => 'Pending Reimbursement upon Delivery',
                        'created_at' => 'Today, 12:10 PM',
                    ],
                ],
            ]);
        }
    }

    public function dashboard()
    {
        $this->initializePrototypeSession();
        $orders = session('customer_orders', []);
        $activeOrders = array_filter($orders, fn($o) => !in_array($o['status'], ['Delivered', 'Cancelled']));
        $cart = session('customer_cart', []);
        $vendors = $this->getVendors();

        return view('customer.dashboard', [
            'activeCount' => count($activeOrders),
            'totalOrders' => count($orders),
            'cartCount' => array_sum(array_column($cart, 'qty')),
            'sukiPoints' => session('suki_points', 145),
            'recentOrders' => array_slice($orders, 0, 3),
            'vendors' => $vendors,
        ]);
    }

    public function vendors(Request $request)
    {
        $vendors = $this->getVendors();
        $search = strtolower($request->query('search', ''));
        $category = $request->query('category', '');

        if ($search) {
            $vendors = array_filter($vendors, fn($v) =>
                str_contains(strtolower($v['name']), $search) ||
                str_contains(strtolower($v['description']), $search)
            );
        }

        if ($category) {
            $vendors = array_filter($vendors, fn($v) => $v['category'] === $category);
        }

        return view('customer.vendors', compact('vendors', 'search', 'category'));
    }

    public function vendorDetails($id)
    {
        $vendors = $this->getVendors();
        $vendor = collect($vendors)->firstWhere('id', (int) $id) ?? $vendors[0];

        return view('customer.vendor-details', compact('vendor'));
    }

    public function cart()
    {
        $this->initializePrototypeSession();
        $cart = session('customer_cart', []);
        $subtotal = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['qty']), 0);

        return view('customer.cart', compact('cart', 'subtotal'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|integer',
            'vendor_name' => 'required|string',
            'item_id' => 'required|integer',
            'item_name' => 'required|string',
            'price' => 'required|numeric|min:1',
            'prep' => 'required|integer',
            'qty' => 'required|integer|min:1|max:20',
        ]);

        $cart = session('customer_cart', []);
        $key = (string) $request->item_id;

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += (int) $request->qty;
        } else {
            $cart[$key] = [
                'vendor_id' => (int) $request->vendor_id,
                'vendor_name' => $request->vendor_name,
                'item_id' => (int) $request->item_id,
                'name' => $request->item_name,
                'price' => (float) $request->price,
                'prep' => (int) $request->prep,
                'qty' => (int) $request->qty,
            ];
        }

        session(['customer_cart' => $cart]);

        return back()->with('success', $request->item_name . ' added to your shopping cart!');
    }

    public function updateCart(Request $request)
    {
        $cart = session('customer_cart', []);
        $key = (string) $request->item_id;

        if (isset($cart[$key])) {
            $cart[$key]['qty'] = max(1, (int) $request->qty);
            session(['customer_cart' => $cart]);
        }

        return back()->with('success', 'Cart quantities updated.');
    }

    public function removeFromCart(Request $request)
    {
        $cart = session('customer_cart', []);
        unset($cart[(string) $request->item_id]);
        session(['customer_cart' => $cart]);

        return back()->with('success', 'Item removed from cart.');
    }

    public function checkout()
    {
        $this->initializePrototypeSession();
        $cart = session('customer_cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.vendors')->with('error', 'Your cart is empty. Select food items first.');
        }

        $subtotal = array_reduce($cart, fn ($sum, $item) => $sum + ($item['price'] * $item['qty']), 0);
        $zones = $this->getDeliveryZones();
        $sukiCredit = session('suki_credit', 0.00);

        return view('customer.checkout', compact('cart', 'subtotal', 'zones', 'sukiCredit'));
    }

    public function placeOrder(Request $request)
    {
        $this->initializePrototypeSession();
        $cart = session('customer_cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.vendors')->with('error', 'Your shopping cart is empty.');
        }

        $validated = $request->validate([
            'zone_id' => 'required|string',
            'delivery_address' => 'required|string|max:255',
            'payment_method' => 'required|in:Cash on Delivery,GCash',
            'apply_suki_credit' => 'nullable|boolean',
            'gcash_ref' => 'nullable|string|max:100',
        ]);

        $zones = $this->getDeliveryZones();
        $zone = collect($zones)->firstWhere('id', $validated['zone_id']) ?? $zones[0];
        $subtotal = array_reduce($cart, fn ($sum, $item) => $sum + ($item['price'] * $item['qty']), 0);
        $maxPrep = max(array_column($cart, 'prep'));
        $vendorNames = implode(', ', array_unique(array_column($cart, 'vendor_name')));
        $itemSummary = implode(', ', array_map(
            fn ($item) => $item['qty'] . 'x ' . $item['name'],
            $cart
        ));

        $discount = 0.00;
        if ($request->boolean('apply_suki_credit') && session('suki_credit', 0) > 0) {
            $discount = min($subtotal, (float) session('suki_credit', 0));
            session(['suki_credit' => 0.00]);
        }

        $total = max(0, $subtotal + $zone['fee'] - $discount);
        $orderNumber = 'BE-2026-' . rand(1050, 9999);
        $isGcash = $validated['payment_method'] === 'GCash';
        $hasRef = !empty($validated['gcash_ref']);

        $newOrder = [
            'order_number' => $orderNumber,
            'type' => 'Food Order',
            'vendor' => $vendorNames,
            'items' => $itemSummary,
            'subtotal' => $subtotal,
            'delivery_fee' => $zone['fee'],
            'discount' => $discount,
            'total' => $total,
            'payment_method' => $validated['payment_method'],
            'payment_status' => $isGcash ? ($hasRef ? 'Awaiting Verification' : 'Pending Payment') : 'Cash on Delivery',
            'gcash_ref' => $validated['gcash_ref'] ?? null,
            'status' => $isGcash ? 'Payment Verification' : 'Pending',
            'zone' => $zone['name'],
            'prep_mins' => $maxPrep,
            'travel_mins' => $zone['travel_mins'],
            'dispatch_buffer' => 5,
            'rider' => 'Awaiting Rider Assignment',
            'created_at' => 'Just now',
        ];

        $orders = session('customer_orders', []);
        array_unshift($orders, $newOrder);

        // Earn Suki points (1 pt per ₱10 spent)
        $earnedPoints = (int) floor($subtotal / 10);
        $newPoints = session('suki_points', 145) + $earnedPoints;
        $ledger = session('suki_ledger', []);
        array_unshift($ledger, [
            'date' => 'Nov 18, 2026',
            'activity' => "Earned from Order #{$orderNumber}",
            'points' => "+{$earnedPoints} pts",
            'type' => 'earn',
        ]);

        session([
            'customer_orders' => $orders,
            'suki_points' => $newPoints,
            'suki_ledger' => $ledger,
            'customer_cart' => [],
        ]);

        return redirect()->route('customer.orders')
            ->with('success', "Order #{$orderNumber} placed successfully! You earned +{$earnedPoints} Suki Points.");
    }

    public function orders()
    {
        $this->initializePrototypeSession();
        $orders = session('customer_orders', []);

        return view('customer.orders', compact('orders'));
    }

    public function submitGcash(Request $request, $orderNumber)
    {
        $this->initializePrototypeSession();
        $request->validate([
            'gcash_ref' => 'required|string|max:100',
            'gcash_proof' => 'nullable|file|max:2048',
        ]);

        $orders = session('customer_orders', []);
        foreach ($orders as &$order) {
            if ($order['order_number'] === $orderNumber) {
                $order['gcash_ref'] = $request->gcash_ref;
                $order['payment_status'] = 'Awaiting Verification';
                $order['status'] = 'Payment Verification';
                break;
            }
        }
        unset($order);

        session(['customer_orders' => $orders]);

        return back()->with('success', "GCash reference ({$request->gcash_ref}) submitted for Order #{$orderNumber}. Vendor verification is now pending.");
    }

    public function tracking($orderNumber = null)
    {
        $this->initializePrototypeSession();
        $orders = session('customer_orders', []);
        $selectedOrder = $orderNumber
            ? collect($orders)->firstWhere('order_number', $orderNumber)
            : ($orders[0] ?? null);

        return view('customer.tracking', compact('orders', 'selectedOrder'));
    }

    public function sukiPoints()
    {
        $this->initializePrototypeSession();

        return view('customer.suki-points', [
            'points' => session('suki_points', 145),
            'credit' => session('suki_credit', 15.00),
            'ledger' => session('suki_ledger', []),
        ]);
    }

    public function redeemPoints(Request $request)
    {
        $this->initializePrototypeSession();
        $request->validate([
            'tier' => 'required|in:50,100,200',
        ]);

        $cost = (int) $request->tier;
        $rewardMap = [50 => 15.00, 100 => 35.00, 200 => 80.00];
        $currentPoints = session('suki_points', 145);

        if ($currentPoints < $cost) {
            return back()->with('error', 'Insufficient Suki Points balance for the selected reward tier.');
        }

        $creditAdded = $rewardMap[$cost];
        $ledger = session('suki_ledger', []);
        array_unshift($ledger, [
            'date' => 'Nov 18, 2026',
            'activity' => 'Redeemed ₱' . number_format($creditAdded, 2) . ' Vendor Discount Credit',
            'points' => "-{$cost} pts",
            'type' => 'redeem',
        ]);

        session([
            'suki_points' => $currentPoints - $cost,
            'suki_credit' => session('suki_credit', 0) + $creditAdded,
            'suki_ledger' => $ledger,
        ]);

        return back()->with('success', 'Redeemed ' . $cost . ' Suki Points for ₱' . number_format($creditAdded, 2) . ' discount credit!');
    }

    public function buyForMe()
    {
        $this->initializePrototypeSession();

        return view('customer.buy-for-me', [
            'requests' => session('buy_for_me_requests', []),
            'zones' => $this->getDeliveryZones(),
        ]);
    }

    public function submitBuyForMe(Request $request)
    {
        $this->initializePrototypeSession();

        $validated = $request->validate([
            'establishment' => 'required|string|max:255',
            'items' => 'required|string|max:500',
            'budget' => 'required|numeric|min:50|max:5000',
            'zone' => 'required|string',
            'delivery_address' => 'required|string|max:255',
        ]);

        $requests = session('buy_for_me_requests', []);
        $requestId = 'BFM-2026-' . rand(100, 999);

        array_unshift($requests, [
            'request_id' => $requestId,
            'establishment' => $validated['establishment'],
            'items' => $validated['items'],
            'budget' => (float) $validated['budget'],
            'actual_expense' => null,
            'zone' => $validated['zone'],
            'delivery_address' => $validated['delivery_address'],
            'rider' => 'Broadcasted to Eligible Zone Riders',
            'status' => 'Pending Rider Acceptance',
            'settlement_status' => 'Unsettled (Reimburse Rider Outside System)',
            'created_at' => 'Just now',
        ]);

        session(['buy_for_me_requests' => $requests]);

        return back()->with('success', "Buy-for-Me Request #{$requestId} created and broadcasted to eligible riders in {$validated['zone']}!");
    }

    public function notifications()
    {
        $notifications = [
            ['title' => 'Order #BE-2026-1042 is Now Preparing', 'message' => 'Nanay Rosa’s Lutong Bahay verified your GCash payment and started preparing your KBL.', 'time' => '5 mins ago', 'unread' => true],
            ['title' => 'Rider Assigned to Buy-for-Me #BFM-2026-088', 'message' => 'Cyrus Ace Taborite purchased your items at Binalbagan Public Market (Actual: ₱265.00).', 'time' => '18 mins ago', 'unread' => true],
            ['title' => 'Suki Points Earned!', 'message' => 'You earned +15 Suki Points from your completed order at San Pedro Batchoy & Grill.', 'time' => 'Yesterday', 'unread' => false],
        ];

        return view('customer.notifications', compact('notifications'));
    }

    public function profile()
    {
        return view('customer.profile', ['user' => Auth::user()]);
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

        return back()->with('success', 'Customer profile updated successfully.');
    }
}