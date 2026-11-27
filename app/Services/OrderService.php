<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OrderService
{
    /**
     * A customer may cancel only while the vendor has not yet responded (status Pending).
     * The row is locked so a simultaneous vendor "accept" cannot be overwritten.
     *
     * @throws OrderException
     */
    public function cancelByCustomer(Order $order, User $customer): void
    {
        DB::transaction(function () use ($order, $customer) {
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ((int) $locked->customer_id !== (int) $customer->id) {
                throw new OrderException('You cannot change this order.');
            }

            if ($locked->order_status !== OrderStatus::Pending) {
                throw new OrderException('This order can no longer be cancelled because the vendor has already responded.');
            }

            $locked->update(['order_status' => OrderStatus::Cancelled]);

            $locked->statusHistories()->create([
                'status' => OrderStatus::Cancelled->value,
                'updated_by' => $customer->id,
            ]);
        });
    }

    /**
     * Stores (or replaces) the GCash reference and screenshot and marks the payment Submitted.
     * Allowed only while the payment is Pending or Rejected.
     *
     * @throws OrderException
     */
    public function submitGcashPayment(Order $order, string $reference, UploadedFile $proof): void
    {
        $newPath = $proof->store('payment-proofs', 'local');
        $oldPath = null;

        try {
            DB::transaction(function () use ($order, $reference, $newPath, &$oldPath) {
                $locked = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
                $payment = Payment::query()->where('order_id', $locked->id)->lockForUpdate()->firstOrFail();

                if ($payment->payment_method !== PaymentMethod::Gcash) {
                    throw new OrderException('This order is not paid with GCash.');
                }

                if ($locked->order_status->isFinal()) {
                    throw new OrderException('This order is already closed.');
                }

                if (! in_array($payment->payment_status, [PaymentStatus::Pending, PaymentStatus::Rejected], true)) {
                    throw new OrderException('Your payment was already submitted or verified.');
                }

                $oldPath = $payment->proof_of_payment;

                $payment->update([
                    'transaction_reference' => $reference,
                    'proof_of_payment' => $newPath,
                    'payment_status' => PaymentStatus::Submitted,
                    'verified_by' => null,
                    'verified_at' => null,
                ]);
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($newPath);

            throw (OrderException::fromDuplicateReference($e) ?? $e);
        }

        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }
    }
}