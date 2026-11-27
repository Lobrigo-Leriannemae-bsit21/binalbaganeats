<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\CartVendorConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AddToCartRequest;
use App\Http\Requests\Customer\UpdateCartRequest;
use App\Models\MenuItem;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    public function index(): View
    {
        $lines = $this->cart->lines();

        return view('customer.cart.index', [
            'lines' => $lines,
            'vendorProfile' => $this->cart->vendorProfile(),
            'subtotal' => $this->cart->subtotal($lines),
            'itemCount' => (int) $lines->where('orderable', true)->sum('quantity'),
            'hasUnavailable' => $lines->contains(fn (array $line) => ! $line['orderable']),
            'maxQuantity' => (int) config('binalbagan.max_item_quantity', 20),
        ]);
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $item = MenuItem::query()
            ->fromVisibleVendors()
            ->with('vendor.vendorProfile')
            ->findOrFail($data['menu_item_id']);

        if (! $item->isOrderable()) {
            return back()->with('error', $item->is_available
                ? 'This store is currently closed.'
                : 'This item is currently unavailable.');
        }

        try {
            $this->cart->add($item, (int) $data['quantity'], $request->boolean('replace'));
        } catch (CartVendorConflictException) {
            // Ask the customer to confirm before replacing the existing cart.
            return redirect()->route('menu.show', $item)
                ->with('cart_conflict', ['quantity' => (int) $data['quantity']]);
        }

        return back()->with('success', $item->item_name.' was added to your cart.');
    }

    public function update(UpdateCartRequest $request, int $item): RedirectResponse
    {
        $this->cart->setQuantity($item, (int) $request->validated('quantity'));

        return redirect()->route('cart.index');
    }

    public function destroy(int $item): RedirectResponse
    {
        $this->cart->remove($item);

        return redirect()->route('cart.index')->with('success', 'Item removed from your cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return redirect()->route('cart.index')->with('success', 'Your cart is now empty.');
    }
}