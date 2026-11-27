<?php

namespace App\Enums;

enum SukiPointType: string
{
    case Earned = 'earned';
    case Redeemed = 'redeemed';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}