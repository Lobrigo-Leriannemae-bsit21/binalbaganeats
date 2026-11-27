<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'Pending';
    case Submitted = 'Submitted';
    case Verified = 'Verified';
    case Rejected = 'Rejected';
    case Paid = 'Paid';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'be-badge-warning',
            self::Submitted => 'be-badge-info',
            self::Verified, self::Paid => 'be-badge-success',
            self::Rejected => 'be-badge-danger',
        };
    }
}