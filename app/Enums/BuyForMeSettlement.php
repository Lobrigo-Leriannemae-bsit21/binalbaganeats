<?php

namespace App\Enums;

/** Payment progress of a Buy-for-Me request (stored in buy_for_me_requests.settlement_status). */
enum BuyForMeSettlement: string
{
    case Unassigned = 'Unassigned';
    case AwaitingAdvance = 'Awaiting Advance';
    case AdvanceSubmitted = 'Advance Submitted';
    case PartiallyPaid = 'Partially Paid';
    case FullyPaid = 'Fully Paid';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Unassigned => 'be-badge-neutral',
            self::AwaitingAdvance => 'be-badge-warning',
            self::AdvanceSubmitted => 'be-badge-info',
            self::PartiallyPaid => 'be-badge-info',
            self::FullyPaid => 'be-badge-success',
        };
    }
}