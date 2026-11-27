<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'Pending';
    case Accepted = 'Accepted';
    case Preparing = 'Preparing';
    case ReadyForPickup = 'Ready for Pickup';
    case OnTheWay = 'On the Way';
    case Delivered = 'Delivered';
    case Declined = 'Declined';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'be-badge-warning',
            self::Accepted, self::Preparing, self::ReadyForPickup, self::OnTheWay => 'be-badge-info',
            self::Delivered => 'be-badge-success',
            self::Declined, self::Cancelled => 'be-badge-danger',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Declined, self::Cancelled], true);
    }

    /** @return array<int, self> Statuses this one may move to. */
    public function nextStatuses(): array
    {
        return match ($this) {
            self::Pending => [self::Accepted, self::Declined, self::Cancelled],
            self::Accepted => [self::Preparing, self::Cancelled],
            self::Preparing => [self::ReadyForPickup, self::Cancelled],
            self::ReadyForPickup => [self::OnTheWay, self::Cancelled],
            self::OnTheWay => [self::Delivered],
            default => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->nextStatuses(), true);
    }

    /** @return array<int, string> Values of all non-final statuses. */
    public static function activeValues(): array
    {
        return array_values(array_map(
            fn (self $status) => $status->value,
            array_filter(self::cases(), fn (self $status) => ! $status->isFinal())
        ));
    }
}