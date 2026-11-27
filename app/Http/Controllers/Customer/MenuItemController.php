<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Contracts\View\View;

class MenuItemController extends Controller
{
    public function show(int $item): View
    {
        // Items of vendors that are not approved are a 404.
        $menuItem = MenuItem::query()
            ->fromVisibleVendors()
            ->with('vendor.vendorProfile')
            ->findOrFail($item);

        $vendor = $menuItem->vendor->vendorProfile;

        $related = $vendor->menuItems()
            ->where('id', '!=', $menuItem->id)
            ->with('vendor.vendorProfile')
            ->orderByDesc('is_available')
            ->orderBy('item_name')
            ->limit(4)
            ->get();

        return view('customer.menu.show', [
            'item' => $menuItem,
            'vendor' => $vendor,
            'orderable' => $menuItem->isOrderable(),
            'related' => $related,
            'maxQuantity' => (int) config('binalbagan.max_item_quantity', 20),
        ]);
    }
}