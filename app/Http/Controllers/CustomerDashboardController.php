<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\MenuItem;
use App\Models\VendorProfile;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function __invoke(Request $request, CartService $cart): View
    {
        $user = $request->user();

        // Open stores first, then by completed deliveries, then oldest first (stable order).
        $popularVendors = VendorProfile::query()
            ->visible()
            ->hasAvailableItems()
            ->withAvailableItemCount()
            ->withCount([
                'orders as delivered_orders_count' => fn ($query) => $query
                    ->where('order_status', OrderStatus::Delivered->value),
            ])
            ->orderByDesc('is_available')
            ->orderByDesc('delivered_orders_count')
            ->orderBy('id')
            ->limit(6)
            ->get();

        $newItems = MenuItem::query()
            ->orderableNow()
            ->with('vendor.vendorProfile')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('customer.dashboard', [
            'user' => $user,
            'totalOrders' => $user->customerOrders()->count(),
            'activeOrders' => $user->customerOrders()
                ->whereIn('order_status', OrderStatus::activeValues())
                ->count(),
            'cartCount' => $cart->count(),
            'categories' => MenuItem::activeCategories(),
            'popularVendors' => $popularVendors,
            'newItems' => $newItems,
        ]);
    }
}