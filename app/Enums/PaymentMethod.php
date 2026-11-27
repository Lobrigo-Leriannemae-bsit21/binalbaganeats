<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case Gcash = 'gcash';

    public function label(): string
    {
        return match ($this) {
            self::Cod => 'Cash on Delivery',
            self::Gcash => 'GCash',
        };
    }
}