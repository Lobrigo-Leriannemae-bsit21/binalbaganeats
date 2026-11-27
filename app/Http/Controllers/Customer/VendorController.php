<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\VendorProfile;
use App\Support\Search;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $q = Search::clean($request->query('q'));
        $category = Search::clean($request->query('category'));

        $vendors = VendorProfile::query()
            ->visible()
            ->hasAvailableItems()
            ->search($q)
            ->withCategory($category)
            ->withAvailableItemCount()
            ->orderByDesc('is_available')
            ->orderBy('business_name')
            ->paginate((int) config('binalbagan.vendors_per_page', 9))
            ->withQueryString();

        return view('customer.vendors.index', [
            'vendors' => $vendors,
            'categories' => MenuItem::activeCategories(),
            'q' => $q,
            'category' => $category,
        ]);
    }

    public function show(Request $request, int $vendor): View
    {
        // Only approved vendors are visible; anything else is a 404.
        $profile = VendorProfile::query()->visible()->findOrFail($vendor);

        $q = Search::clean($request->query('q'));
        $category = Search::clean($request->query('category'));

        $items = $profile->menuItems()
            ->when($q, function ($query, string $term) {
                $like = Search::like($term);

                $query->where(fn ($w) => $w
                    ->where('item_name', 'like', $like)
                    ->orWhere('description', 'like', $like));
            })
            ->when($category, fn ($query, string $value) => $query->where('category', $value))
            ->with('vendor.vendorProfile')
            ->orderByDesc('is_available')
            ->orderBy('item_name')
            ->get();

        // Group the dishes by category, in the configured display order.
        $grouped = MenuItem::sortCategories($items->pluck('category'))
            ->mapWithKeys(fn (string $name) => [$name => $items->where('category', $name)->values()]);

        $storeCategories = MenuItem::sortCategories(
            $profile->menuItems()->distinct()->pluck('category')
        );

        return view('customer.vendors.show', [
            'vendor' => $profile,
            'grouped' => $grouped,
            'storeCategories' => $storeCategories,
            'itemCount' => $items->count(),
            'q' => $q,
            'category' => $category,
        ]);
    }
}