<?php

namespace App\Enums;

enum UserStatus: string
{
    case Approved = 'approved';
    case Pending = 'pending';
    case Rejected = 'rejected';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Approved => 'be-badge-success',
            self::Pending => 'be-badge-warning',
            self::Rejected => 'be-badge-danger',
        };
    }
}