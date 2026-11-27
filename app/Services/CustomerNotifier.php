<?php

namespace App\Services;

use App\Enums\BuyForMeStatus;
use App\Enums\OrderStatus;
use App\Models\BuyForMeRequest;
use App\Models\Order;
use App\Models\User;
use App\Notifications\CustomerNotice;
use Throwable;

/**
 * Builds and sends every customer notification. A failure here is logged but never
 * interrupts the order or request that caused it.
 */
class CustomerNotifier
{
    // ------------------------------------------------------------------ orders

    public function orderStatusChanged(Order $order, OrderStatus $status, ?User $actor = null): void
    {
        // Do not tell customers about changes they made themselves.
        if ($actor !== null && (int) $actor->id === (int) $order->customer_id) {
            return;
        }

        $number = $order->order_number;
        $returned = (float) $order->discount_amount > 0 ? ' The Suki Points you used were returned.' : '';

        $content = match ($status) {
            OrderStatus::Accepted => ['Order accepted', "{$number} was accepted by the vendor.", 'bi-check-circle'],
            OrderStatus::Preparing => ['Preparing your order', "The vendor is now preparing {$number}.", 'bi-fire'],
            OrderStatus::OnTheWay => ['Your order is on the way!', "{$number} is on its way to you.", 'bi-bicycle'],
            OrderStatus::Delivered => ['Order delivered', "{$number} was delivered. Enjoy your meal!", 'bi-bag-check'],
            OrderStatus::Declined => ['Order declined', "The vendor could not accept {$number}.{$returned}", 'bi-x-circle'],
            OrderStatus::Cancelled => ['Order cancelled', "{$number} was cancelled.{$returned}", 'bi-x-circle'],
            default => null,
        };

        if ($content === null) {
            return;
        }

        [$title, $message, $icon] = $content;

        $url = $status->isFinal() || $status === OrderStatus::Accepted
            ? route('orders.show', $order, false)
            : route('orders.track', $order, false);

        $this->send($order->customer, $title, $message, $url, $icon, 'order');
    }

    public function pointsEarned(Order $order, int $points): void
    {
        $this->send(
            $order->customer,
            'You earned '.number_format($points).' Suki '.($points === 1 ? 'Point' : 'Points').'!',
            "From order {$order->order_number}. They are now available to use.",
            route('suki.index', [], false),
            'bi-gift',
            'points'
        );
    }

    /** Called by the vendor's payment review in Phase 2. */
    public function paymentReviewed(Order $order, bool $verified): void
    {
        $this->send(
            $order->customer,
            $verified ? 'Payment verified' : 'Payment could not be verified',
            $verified
                ? "Your GCash payment for {$order->order_number} was verified."
                : "Your GCash payment for {$order->order_number} was rejected. Please check your details and submit them again.",
            route('orders.show', $order, false),
            $verified ? 'bi-patch-check' : 'bi-exclamation-triangle',
            'payment'
        );
    }

    // ------------------------------------------------------------- buy-for-me

    public function buyForMeStatusChanged(BuyForMeRequest $request, BuyForMeStatus $status, ?User $actor = null): void
    {
        if ($actor !== null && (int) $actor->id === (int) $request->customer_id) {
            return;
        }

        $code = $request->request_code;
        $peso = fn (?int $cents) => '₱'.number_format(($cents ?? 0) / 100, 2);

        $content = match ($status) {
            BuyForMeStatus::Accepted => [
                'Rider accepted your request',
                ($request->rider?->name ?? 'A rider')." accepted {$code} and will confirm the estimated cost.",
                'bi-person-check',
            ],
            BuyForMeStatus::AwaitingAdvance => [
                'Send your advance payment',
                'Estimated total '.$peso($request->estimatedTotalCents()).'. Please send '.$peso($request->advanceCents()).' by GCash so your rider can buy your items.',
                'bi-phone',
            ],
            BuyForMeStatus::Purchasing => [
                'Advance payment confirmed',
                "Your rider is now buying the items for {$code}.",
                'bi-bag',
            ],
            BuyForMeStatus::OnTheWay => [
                'Your items are on the way!',
                "Your rider bought the items for {$code} and is heading to you.",
                'bi-bicycle',
            ],
            BuyForMeStatus::Delivered => [
                'Request delivered',
                "{$code} was delivered. Thank you for using Buy-for-Me!",
                'bi-bag-check',
            ],
            BuyForMeStatus::Cancelled => [
                'Request cancelled',
                "{$code} was cancelled.",
                'bi-x-circle',
            ],
            default => null,
        };

        if ($content === null) {
            return;
        }

        [$title, $message, $icon] = $content;

        $this->send($request->customer, $title, $message, route('buyforme.show', $request, false), $icon, 'buyforme');
    }

    public function buyForMeAdvanceRejected(BuyForMeRequest $request): void
    {
        $this->send(
            $request->customer,
            'Advance payment not received',
            "Your rider could not find your GCash payment for {$request->request_code}. Please check your details and submit them again.",
            route('buyforme.show', $request, false),
            'bi-exclamation-triangle',
            'buyforme'
        );
    }

    // ------------------------------------------------------------------ helper

    private function send(?User $customer, string $title, string $message, string $url, string $icon, string $category): void
    {
        if (! $customer) {
            return;
        }

        try {
            $customer->notify(new CustomerNotice($title, $message, $url, $icon, $category));
        } catch (Throwable $e) {
            report($e); // never let a notification problem break the order flow
        }
    }
}