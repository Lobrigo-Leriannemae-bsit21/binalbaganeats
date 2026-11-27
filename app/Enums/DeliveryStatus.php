<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case ReadyForPickup = 'Ready for Pickup';
    case Accepted = 'Accepted';
    case PickedUp = 'Picked Up';
    case OnTheWay = 'On the Way';
    case Delivered = 'Delivered';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ReadyForPickup => 'be-badge-warning',
            self::Accepted, self::PickedUp, self::OnTheWay => 'be-badge-info',
            self::Delivered => 'be-badge-success',
        };
    }

    /** @return array<int, self> */
    public function nextStatuses(): array
    {
        return match ($this) {
            self::ReadyForPickup => [self::Accepted],
            self::Accepted => [self::PickedUp],
            self::PickedUp => [self::OnTheWay],
            self::OnTheWay => [self::Delivered],
            self::Delivered => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->nextStatuses(), true);
    }
}