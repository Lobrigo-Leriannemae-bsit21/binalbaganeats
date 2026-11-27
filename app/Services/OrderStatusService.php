<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\SukiPointStatus;
use App\Enums\SukiPointType;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\SukiPointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * The ONE place an order's status changes. It enforces the allowed order of statuses,
 * writes the status history, finalizes or releases Suki Points, and notifies the customer.
 * Customers (cancel), vendors (Phase 2) and riders (Phase 3) will all use it.
 */
class OrderStatusService
{
    public function __construct(
        private readonly SukiPointService $suki,
        private readonly CustomerNotifier $notifier,
    ) {
    }

    /** @throws OrderException */
    public function transition(Order $order, OrderStatus $next, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $next, $actor) {
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (! $locked->order_status->canTransitionTo($next)) {
                throw new OrderException(
                    "An order that is {$locked->order_status->label()} cannot change to {$next->label()}."
                );
            }

            $locked->update(['order_status' => $next]);

            $locked->statusHistories()->create([
                'status' => $next->value,
                'updated_by' => $actor?->id,
            ]);

            match ($next) {
                OrderStatus::Delivered => $this->suki->finalizeForOrder($locked),
                OrderStatus::Declined, OrderStatus::Cancelled => $this->suki->releasePendingForOrder($locked),
                default => null,
            };

            $earned = $next === OrderStatus::Delivered
                ? (int) SukiPointTransaction::query()
                    ->where('order_id', $locked->id)
                    ->where('type', SukiPointType::Earned->value)
                    ->where('status', SukiPointStatus::Approved->value)
                    ->sum('points_amount')
                : 0;

            // Notify only after the change is safely saved.
            DB::afterCommit(function () use ($locked, $next, $actor, $earned) {
                $this->notifier->orderStatusChanged($locked, $next, $actor);

                if ($earned > 0) {
                    $this->notifier->pointsEarned($locked, $earned);
                }
            });

            return $locked;
        });
    }
}