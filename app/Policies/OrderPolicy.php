<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /** Customers may only see their own orders. */
    public function view(User $user, Order $order): bool
    {
        return $user->isCustomer() && (int) $order->customer_id === (int) $user->id;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $this->view($user, $order) && $order->order_status === OrderStatus::Pending;
    }

    public function submitPayment(User $user, Order $order): bool
    {
        if (! $this->view($user, $order) || $order->order_status->isFinal()) {
            return false;
        }

        $payment = $order->payment;

        return $payment !== null
            && $payment->payment_method === PaymentMethod::Gcash
            && in_array($payment->payment_status, [PaymentStatus::Pending, PaymentStatus::Rejected], true);
    }
}