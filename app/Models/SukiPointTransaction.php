<?php

namespace App\Models;

use App\Enums\SukiPointStatus;
use App\Enums\SukiPointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SukiPointTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'order_id',
        'points_amount',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'points_amount' => 'integer',
            'type' => SukiPointType::class,
            'status' => SukiPointStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** +points for earned, -points for redeemed. */
    public function signedPoints(): int
    {
        return $this->type === SukiPointType::Earned ? $this->points_amount : -$this->points_amount;
    }

    /** Customer-friendly status text. */
    public function statusLabel(): string
    {
        return match (true) {
            $this->type === SukiPointType::Earned && $this->status === SukiPointStatus::Pending => 'Pending delivery',
            $this->type === SukiPointType::Earned => 'Available',
            $this->status === SukiPointStatus::Pending => 'On hold',
            default => 'Used',
        };
    }

    public function badgeClass(): string
    {
        return match ($this->statusLabel()) {
            'Pending delivery' => 'be-badge-warning',
            'Available' => 'be-badge-success',
            'On hold' => 'be-badge-info',
            default => 'be-badge-neutral',
        };
    }
}