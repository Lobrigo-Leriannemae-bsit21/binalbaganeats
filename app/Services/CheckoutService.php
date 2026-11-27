<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\OrderException;
use App\Models\DeliveryZone;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Turns the session cart into an order. Everything that matters (prices, availability,
 * delivery fee, Suki Points discount, totals) is re-read from the database inside one
 * transaction.
 */
class CheckoutService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly SukiPointService $suki,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data  Validated checkout data.
     *
     * @throws OrderException
     */
    public function placeOrder(User $customer, array $data, ?UploadedFile $proof): Order
    {
        $method = PaymentMethod::from($data['payment_method']);
        $isGcash = $method === PaymentMethod::Gcash;

        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            throw new OrderException('Your cart is empty.');
        }

        $quantities = $lines->mapWithKeys(fn (array $line) => [$line['item']->id => $line['quantity']]);

        // Payment proofs are kept on the PRIVATE disk (storage/app/private).
        $proofPath = null;
        if ($isGcash) {
            if (! $proof) {
                throw new OrderException('Please upload your GCash payment screenshot.');
            }

            $proofPath = $proof->store('payment-proofs', 'local');
        }

        try {
            $order = DB::transaction(
                fn () => $this->createOrder($customer, $data, $method, $quantities, $proofPath)
            );
        } catch (Throwable $e) {
            if ($proofPath !== null) {
                Storage::disk('local')->delete($proofPath);
            }

            throw (OrderException::fromDuplicateReference($e) ?? $e);
        }

        $this->cart->clear();

        return $order;
    }

    /** @param Collection<int, int> $quantities menu_item_id => quantity */
    private function createOrder(
        User $customer,
        array $data,
        PaymentMethod $method,
        Collection $quantities,
        ?string $proofPath
    ): Order {
        // Lock the menu rows so prices and availability cannot change mid-checkout.
        $items = MenuItem::query()
            ->whereIn('id', $quantities->keys()->all())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($items->count() !== $quantities->count()) {
            throw new OrderException('Some items in your cart are no longer available. Please review your cart.');
        }

        $items->load('vendor.vendorProfile');

        foreach ($items as $item) {
            if (! $item->isOrderable()) {
                throw new OrderException($item->item_name.' can no longer be ordered. Please update your cart.');
            }
        }

        $vendorIds = $items->pluck('vendor_id')->unique();

        if ($vendorIds->count() !== 1) {
            throw new OrderException('Your cart contains items from more than one vendor.');
        }

        $vendorId = (int) $vendorIds->first();
        $profile = $items->first()->vendor->vendorProfile;

        if ($method === PaymentMethod::Gcash && blank($profile->gcash_number)) {
            throw new OrderException('This vendor does not accept GCash. Please choose another payment method.');
        }

        $zone = DeliveryZone::query()->active()->find((int) $data['delivery_zone_id']);

        if (! $zone) {
            throw new OrderException('The selected delivery zone is no longer available. Please choose another.');
        }

        // Work in whole centavos to avoid floating-point rounding errors.
        $subtotalCents = 0;
        foreach ($quantities as $id => $quantity) {
            $subtotalCents += (int) round(((float) $items[$id]->price) * 100) * $quantity;
        }
        $feeCents = (int) round(((float) $zone->delivery_fee) * 100);

        // Suki Points: lock the customer row first so two checkouts cannot spend the same points.
        $points = max(0, (int) ($data['use_points'] ?? 0));

        if ($points > 0) {
            User::query()->whereKey($customer->id)->lockForUpdate()->first();
            $this->suki->assertCanRedeem($customer, $points, $subtotalCents);
        }

        // The discount applies to food only (max 50% of the subtotal), never to the delivery fee.
        $discountCents = $points * $this->suki->valueCents();
        $totalCents = $subtotalCents + $feeCents - $discountCents;

        $order = Order::create([
            'order_number' => 'TMP-'.Str::random(20), // replaced below once the id is known
            'customer_id' => $customer->id,
            'vendor_id' => $vendorId,
            'delivery_zone_id' => $zone->id,
            'delivery_address' => $data['delivery_address'],
            'subtotal' => $this->money($subtotalCents),
            'delivery_fee' => $this->money($feeCents),
            'discount_amount' => $this->money($discountCents),
            'total_amount' => $this->money($totalCents),
            'order_status' => OrderStatus::Pending,
        ]);

        $order->update(['order_number' => sprintf('BE-%06d', $order->id)]);

        foreach ($quantities as $id => $quantity) {
            $order->items()->create([
                'menu_item_id' => $id,
                'quantity' => $quantity,
                'unit_price' => $items[$id]->price, // price captured at order time
            ]);
        }

        $isGcash = $method === PaymentMethod::Gcash;

        $order->payment()->create([
            'payment_method' => $method,
            'payment_status' => $isGcash ? PaymentStatus::Submitted : PaymentStatus::Pending,
            'transaction_reference' => $isGcash ? ($data['transaction_reference'] ?? null) : null,
            'proof_of_payment' => $proofPath,
        ]);

        $order->statusHistories()->create([
            'status' => OrderStatus::Pending->value,
            'updated_by' => $customer->id,
        ]);

        if ($points > 0) {
            $this->suki->holdPoints($order, $points);
        }

        $this->suki->queueEarnedPoints($order, $subtotalCents);

        if (! empty($data['save_address'])) {
            $customer->update(['address' => $data['delivery_address']]);
        }

        return $order;
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}