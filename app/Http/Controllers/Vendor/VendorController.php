<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    private function initializeVendorSession(): void
    {
        if (!session()->has('vendor_storefront')) {
            session([
                'vendor_storefront' => [
                    'name' => Auth::user()->storefront_name ?? 'Nanay Rosa’s Lutong Bahay',
                    'barangay' => 'Brgy. San Pedro, Binalbagan',
                    'address' => Auth::user()->address ?? 'Poblacion Market Road, Brgy. San Pedro',
                    'category' => 'Carinderia & Rice Meals',
                    'prep_time' => 15,
                    'status' => 'Available',
                    'operating_hours' => '7:00 AM – 7:30 PM',
                    'gcash_number' => '0917-333-4444',
                    'gcash_name' => 'Rosa M. (Nanay Rosa Carinderia)',
                    'description' => 'Authentic Negrense home-cooked viands, KBL, and freshly steamed rice along Poblacion Market Road.',
                ],
                'vendor_menu' => [
                    ['id' => 101, 'name' => 'Kadyos, Baboy, Langka (KBL)', 'category' => 'Main Viand', 'price' => 85.00, 'prep' => 15, 'available' => true, 'desc' => 'Classic Ilonggo pigeon peas soup with pork hock and jackfruit.'],
                    ['id' => 102, 'name' => 'Pork Adobo with Egg & Rice', 'category' => 'Rice Meal', 'price' => 75.00, 'prep' => 10, 'available' => true, 'desc' => 'Savory braised pork belly served with boiled egg and steamed rice.'],
                    ['id' => 103, 'name' => 'Sinigang na Bangus Bowl', 'category' => 'Main Viand', 'price' => 95.00, 'prep' => 20, 'available' => true, 'desc' => 'Sour tamarind milkfish soup loaded with local vegetables.'],
                    ['id' => 104, 'name' => 'Dinuguan with Puto', 'category' => 'Merienda', 'price' => 70.00, 'prep' => 12, 'available' => false, 'desc' => 'Rich pork stew paired with steamed rice cakes.'],
                ],
                'vendor_orders' => [
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
                ],
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
            'id' => rand(200, 999),
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