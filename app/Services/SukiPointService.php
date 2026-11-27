<?php

namespace App\Services;

use App\Enums\SukiPointStatus;
use App\Enums\SukiPointType;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\SukiPointTransaction;
use App\Models\User;

/**
 * All Suki Points rules live here (numbers come from config/suki.php).
 *
 * Row lifecycle (suki_point_transactions):
 *   earned   pending  -> created at checkout         (not spendable yet)
 *   earned   approved -> when the order is Delivered (spendable)
 *   redeemed pending  -> created at checkout         (held, cannot be spent twice)
 *   redeemed applied  -> when the order is Delivered (used)
 * Cancelled / declined orders: the pending rows are deleted (points returned / never earned).
 *
 * Balance = earned(approved) - redeemed(pending + applied)
 */
class SukiPointService
{
    // ---- Rules ----

    public function valueCents(): int
    {
        return max(1, (int) round(((float) config('suki.redeem_value_pesos', 1)) * 100));
    }

    public function minRedeem(): int
    {
        return max(1, (int) config('suki.min_redeem_points', 10));
    }

    public function maxDiscountPercent(): int
    {
        return max(0, min(100, (int) config('suki.max_discount_percent', 50)));
    }

    public function earnPesosPerPoint(): int
    {
        return max(1, (int) config('suki.earn_peso_per_point', 50));
    }

    /** @return array{earn_peso_per_point: int, value_pesos: float, min_redeem: int, max_discount_percent: int} */
    public function rules(): array
    {
        return [
            'earn_peso_per_point' => $this->earnPesosPerPoint(),
            'value_pesos' => $this->valueCents() / 100,
            'min_redeem' => $this->minRedeem(),
            'max_discount_percent' => $this->maxDiscountPercent(),
        ];
    }

    /** Points earned for a food subtotal (in centavos). Whole points only. */
    public function pointsForSubtotalCents(int $subtotalCents): int
    {
        return intdiv(max(0, $subtotalCents), $this->earnPesosPerPoint() * 100);
    }

    /** The most points usable on an order, given the balance and food subtotal (centavos). */
    public function maxRedeemable(int $balance, int $subtotalCents): int
    {
        $capCents = intdiv(max(0, $subtotalCents) * $this->maxDiscountPercent(), 100);
        $capPoints = intdiv($capCents, $this->valueCents());

        return max(0, min($balance, $capPoints));
    }

    // ---- Balances ----

    /** Points the customer can spend right now. */
    public function availableBalance(User $user): int
    {
        $earned = $this->sum($user, SukiPointType::Earned, [SukiPointStatus::Approved]);
        $spent = $this->sum($user, SukiPointType::Redeemed, [SukiPointStatus::Pending, SukiPointStatus::Applied]);

        return max(0, $earned - $spent);
    }

    /** Earned on placed orders, waiting for delivery. */
    public function pendingEarned(User $user): int
    {
        return $this->sum($user, SukiPointType::Earned, [SukiPointStatus::Pending]);
    }

    /** Reserved by orders that are not delivered yet. */
    public function heldPoints(User $user): int
    {
        return $this->sum($user, SukiPointType::Redeemed, [SukiPointStatus::Pending]);
    }

    public function lifetimeEarned(User $user): int
    {
        return $this->sum($user, SukiPointType::Earned, [SukiPointStatus::Approved]);
    }

    public function usedPoints(User $user): int
    {
        return $this->sum($user, SukiPointType::Redeemed, [SukiPointStatus::Applied]);
    }

    // ---- Checkout ----

    /**
     * Validates a redemption request. Call inside the checkout transaction AFTER locking
     * the customer row, so two simultaneous checkouts cannot spend the same points.
     *
     * @throws OrderException
     */
    public function assertCanRedeem(User $user, int $points, int $subtotalCents): void
    {
        $balance = $this->availableBalance($user);
        $min = $this->minRedeem();
        $max = $this->maxRedeemable($balance, $subtotalCents);

        if ($points > $balance) {
            throw new OrderException("You only have {$balance} Suki Points available.");
        }

        if ($points < $min) {
            throw new OrderException("You need to use at least {$min} Suki Points to redeem.");
        }

        if ($max < $min) {
            throw new OrderException('This order is too small to use Suki Points.');
        }

        if ($points > $max) {
            throw new OrderException(
                "You can use at most {$max} Suki Points on this order (up to {$this->maxDiscountPercent()}% of the food subtotal)."
            );
        }
    }

    /** Reserves redeemed points for an order (creates a "redeemed / pending" row). */
    public function holdPoints(Order $order, int $points): void
    {
        SukiPointTransaction::create([
            'customer_id' => $order->customer_id,
            'order_id' => $order->id,
            'points_amount' => $points,
            'type' => SukiPointType::Redeemed,
            'status' => SukiPointStatus::Pending,
        ]);
    }

    /** Records the points this order will earn once delivered. Returns the amount (may be 0). */
    public function queueEarnedPoints(Order $order, int $subtotalCents): int
    {
        $points = $this->pointsForSubtotalCents($subtotalCents);

        if ($points > 0) {
            SukiPointTransaction::create([
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'points_amount' => $points,
                'type' => SukiPointType::Earned,
                'status' => SukiPointStatus::Pending,
            ]);
        }

        return $points;
    }

    // ---- Order lifecycle ----

    /** Order delivered: earned points become spendable, held points become used. */
    public function finalizeForOrder(Order $order): void
    {
        SukiPointTransaction::query()
            ->where('order_id', $order->id)
            ->where('type', SukiPointType::Earned->value)
            ->where('status', SukiPointStatus::Pending->value)
            ->update(['status' => SukiPointStatus::Approved->value]);

        SukiPointTransaction::query()
            ->where('order_id', $order->id)
            ->where('type', SukiPointType::Redeemed->value)
            ->where('status', SukiPointStatus::Pending->value)
            ->update(['status' => SukiPointStatus::Applied->value]);
    }

    /** Order cancelled or declined: held points are returned and nothing is earned. */
    public function releasePendingForOrder(Order $order): void
    {
        SukiPointTransaction::query()
            ->where('order_id', $order->id)
            ->where('status', SukiPointStatus::Pending->value)
            ->delete();
    }

    /** @param array<int, SukiPointStatus> $statuses */
    private function sum(User $user, SukiPointType $type, array $statuses): int
    {
        return (int) SukiPointTransaction::query()
            ->where('customer_id', $user->id)
            ->where('type', $type->value)
            ->whereIn('status', array_map(fn (SukiPointStatus $status) => $status->value, $statuses))
            ->sum('points_amount');
    }
}