<?php

namespace App\Enums;

enum SukiPointStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Applied = 'applied';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}