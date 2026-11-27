<?php

namespace App\Models;

use App\Enums\BuyForMeSettlement;
use App\Enums\BuyForMeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyForMeRequest extends Model
{
    // Created and changed only by BuyForMeService, never from raw request data.
    protected $fillable = [
        'request_code',
        'customer_id',
        'delivery_zone_id',
        'rider_id',
        'establishment_name',
        'items_summary',
        'delivery_address',
        'maximum_budget',          // no longer used (kept for the original schema)
        'actual_expense',
        'estimated_expense',
        'delivery_fee',
        'advance_amount',
        'advance_confirmed_at',
        'gcash_reference_number',
        'advance_proof',
        'receipt_image',
        'request_status',
        'settlement_status',
    ];

    protected function casts(): array
    {
        return [
            'maximum_budget' => 'decimal:2',
            'actual_expense' => 'decimal:2',
            'estimated_expense' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'advance_confirmed_at' => 'datetime',
            'request_status' => BuyForMeStatus::class,
            'settlement_status' => BuyForMeSettlement::class,
        ];
    }

    // ---- Relationships ----

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }

    // ---- Money (all maths in whole centavos to avoid rounding errors) ----

    public function feeCents(): int
    {
        return self::cents($this->delivery_fee);
    }

    public function advanceCents(): int
    {
        return self::cents($this->advance_amount);
    }

    /** Estimated food + delivery fee, or null until the rider confirms an estimate. */
    public function estimatedTotalCents(): ?int
    {
        return $this->estimated_expense === null
            ? null
            : self::cents($this->estimated_expense) + $this->feeCents();
    }

    /** Actual food + delivery fee, or null until the rider records the purchase. */
    public function finalTotalCents(): ?int
    {
        return $this->actual_expense === null
            ? null
            : self::cents($this->actual_expense) + $this->feeCents();
    }

    public function advanceConfirmed(): bool
    {
        return $this->advance_confirmed_at !== null;
    }

    /**
     * What is still owed after the confirmed advance (negative = the rider returns change).
     * Uses the final total once known, otherwise the estimate.
     */
    public function remainingCents(): ?int
    {
        $total = $this->finalTotalCents() ?? $this->estimatedTotalCents();

        if ($total === null) {
            return null;
        }

        return $total - ($this->advanceConfirmed() ? $this->advanceCents() : 0);
    }

    private static function cents(mixed $value): int
    {
        return (int) round(((float) $value) * 100);
    }
}