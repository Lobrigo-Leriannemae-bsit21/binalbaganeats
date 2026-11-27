<?php

namespace App\Enums;

enum BuyForMeStatus: string
{
    case Open = 'Open Request';
    case Accepted = 'Accepted';
    case AwaitingAdvance = 'Awaiting Advance Payment';
    case Purchasing = 'Purchasing';
    case OnTheWay = 'On the Way';
    case Delivered = 'Delivered';
    case Cancelled = 'Cancelled';

    /** The six customer-facing stages shown on the tracker. */
    public const STAGES = [
        1 => 'Request Submitted',
        2 => 'Rider Accepted',
        3 => 'Advance Payment',
        4 => 'Buying Your Items',
        5 => 'On the Way',
        6 => 'Delivered',
    ];

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Open => 'be-badge-warning',
            self::Accepted, self::AwaitingAdvance, self::Purchasing, self::OnTheWay => 'be-badge-info',
            self::Delivered => 'be-badge-success',
            self::Cancelled => 'be-badge-danger',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled], true);
    }

    /** The tracker stage (1-6) this status has reached, or null for Cancelled. */
    public function stage(): ?int
    {
        return match ($this) {
            self::Open => 1,
            self::Accepted => 2,
            self::AwaitingAdvance => 3,
            self::Purchasing => 4,
            self::OnTheWay => 5,
            self::Delivered => 6,
            self::Cancelled => null,
        };
    }

    public function message(): string
    {
        return match ($this) {
            self::Open => 'Waiting for a rider in your barangay to accept your request.',
            self::Accepted => 'A rider accepted your request and will confirm the estimated cost.',
            self::AwaitingAdvance => 'Please send the advance payment by GCash so the rider can buy your items.',
            self::Purchasing => 'Your advance was confirmed. The rider is buying your items.',
            self::OnTheWay => 'Your items were bought and are on the way!',
            self::Delivered => 'Your items were delivered. Thank you!',
            self::Cancelled => 'This request was cancelled.',
        };
    }

    /** @return array<int, self> */
    public function nextStatuses(): array
    {
        return match ($this) {
            self::Open => [self::Accepted, self::Cancelled],
            self::Accepted => [self::AwaitingAdvance, self::Cancelled],
            self::AwaitingAdvance => [self::Purchasing, self::Cancelled],
            self::Purchasing => [self::OnTheWay],
            self::OnTheWay => [self::Delivered],
            default => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->nextStatuses(), true);
    }

    /** Statuses in which the customer may still cancel (and only until the advance is submitted). */
    public function customerCancellable(): bool
    {
        return in_array($this, [self::Open, self::Accepted, self::AwaitingAdvance], true);
    }

    /** @return array<int, string> Values of every status that is not final. */
    public static function activeValues(): array
    {
        return array_values(array_map(
            fn (self $status) => $status->value,
            array_filter(self::cases(), fn (self $status) => ! $status->isFinal())
        ));
    }
}