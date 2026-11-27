<?php

namespace App\Support;

use App\Enums\OrderStatus;

final class OrderTracker
{
    /** The four customer-facing stages shown in the design reference. */
    public const STAGES = [
        1 => 'Order Submitted',
        2 => 'Preparing Order',
        3 => 'On the Way',
        4 => 'Delivered',
    ];

    /** The stage (1-4) an order has reached, or null for Declined / Cancelled. */
    public static function stage(OrderStatus $status): ?int
    {
        return match ($status) {
            OrderStatus::Pending, OrderStatus::Accepted => 1,
            OrderStatus::Preparing, OrderStatus::ReadyForPickup => 2,
            OrderStatus::OnTheWay => 3,
            OrderStatus::Delivered => 4,
            OrderStatus::Declined, OrderStatus::Cancelled => null,
        };
    }

    public static function message(OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::Pending => 'Waiting for the vendor to accept your order.',
            OrderStatus::Accepted => 'The vendor accepted your order.',
            OrderStatus::Preparing => 'Your food is being prepared.',
            OrderStatus::ReadyForPickup => 'Your order is ready and waiting for a rider.',
            OrderStatus::OnTheWay => 'Your order is on the way!',
            OrderStatus::Delivered => 'Your order was delivered. Enjoy your meal!',
            OrderStatus::Declined => 'The vendor could not accept this order.',
            OrderStatus::Cancelled => 'This order was cancelled.',
        };
    }
}