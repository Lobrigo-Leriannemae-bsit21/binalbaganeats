<?php

namespace App\Services;

use App\Exceptions\CartVendorConflictException;
use App\Models\MenuItem;
use App\Models\VendorProfile;
use Illuminate\Support\Collection;

/**
 * Session-backed cart. The session stores ONLY ids and quantities:
 *   ['vendor_id' => 5, 'items' => [menu_item_id => quantity]]
 * Prices, names and availability are always read fresh from the database.
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    /** Total number of units in the cart. */
    public function count(): int
    {
        return (int) array_sum($this->read()['items']);
    }

    public function isEmpty(): bool
    {
        return $this->read()['items'] === [];
    }

    public function vendorId(): ?int
    {
        return $this->read()['vendor_id'];
    }

    /**
     * @throws CartVendorConflictException when the cart holds another vendor's items and $replace is false
     */
    public function add(MenuItem $item, int $quantity, bool $replace = false): void
    {
        $cart = $this->read();

        if ($cart['items'] !== [] && $cart['vendor_id'] !== null && $cart['vendor_id'] !== (int) $item->vendor_id) {
            if (! $replace) {
                throw new CartVendorConflictException();
            }

            $cart = ['vendor_id' => null, 'items' => []];
        }

        $cart['vendor_id'] = (int) $item->vendor_id;
        $cart['items'][$item->id] = $this->clamp(($cart['items'][$item->id] ?? 0) + $quantity);

        $this->write($cart);
    }

    public function setQuantity(int $menuItemId, int $quantity): void
    {
        $cart = $this->read();

        if (! isset($cart['items'][$menuItemId])) {
            return;
        }

        $cart['items'][$menuItemId] = $this->clamp($quantity);

        $this->write($cart);
    }

    public function remove(int $menuItemId): void
    {
        $cart = $this->read();
        unset($cart['items'][$menuItemId]);

        $this->write($cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * @return Collection<int, array{item: MenuItem, quantity: int, orderable: bool, line_total: float}>
     */
    public function lines(): Collection
    {
        $cart = $this->read();

        if ($cart['items'] === []) {
            return collect();
        }

        $items = MenuItem::query()
            ->with('vendor.vendorProfile')
            ->whereIn('id', array_keys($cart['items']))
            ->get()
            ->keyBy('id');

        // Forget entries whose menu item has been deleted since it was added.
        $missing = array_diff(array_keys($cart['items']), $items->keys()->all());
        if ($missing !== []) {
            foreach ($missing as $id) {
                unset($cart['items'][$id]);
            }
            $this->write($cart);
        }

        return collect($cart['items'])
            ->map(function (int $quantity, int $id) use ($items) {
                $item = $items->get($id);

                if (! $item) {
                    return null;
                }

                return [
                    'item' => $item,
                    'quantity' => $quantity,
                    'orderable' => $item->isOrderable(),
                    'line_total' => round((float) $item->price * $quantity, 2),
                ];
            })
            ->filter()
            ->values();
    }

    /** Sum of the lines that can currently be ordered. */
    public function subtotal(?Collection $lines = null): float
    {
        $lines ??= $this->lines();

        return round((float) $lines->where('orderable', true)->sum('line_total'), 2);
    }

    public function vendorProfile(): ?VendorProfile
    {
        $vendorId = $this->vendorId();

        return $vendorId
            ? VendorProfile::query()->where('user_id', $vendorId)->first()
            : null;
    }

    /** Reads the session defensively: anything malformed is discarded. */
    private function read(): array
    {
        $cart = session()->get(self::SESSION_KEY, []);
        $items = [];

        foreach ((array) ($cart['items'] ?? []) as $id => $quantity) {
            $id = (int) $id;
            $quantity = (int) $quantity;

            if ($id > 0 && $quantity > 0) {
                $items[$id] = $this->clamp($quantity);
            }
        }

        return [
            'vendor_id' => ($items !== [] && isset($cart['vendor_id'])) ? (int) $cart['vendor_id'] : null,
            'items' => $items,
        ];
    }

    private function write(array $cart): void
    {
        if ($cart['items'] === []) {
            session()->forget(self::SESSION_KEY);

            return;
        }

        session()->put(self::SESSION_KEY, $cart);
    }

    private function clamp(int $quantity): int
    {
        return max(1, min((int) config('binalbagan.max_item_quantity', 20), $quantity));
    }
}