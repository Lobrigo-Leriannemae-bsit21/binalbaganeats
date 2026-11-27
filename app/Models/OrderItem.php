<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    // order_id is set through $order->items()->create(...).
    protected $fillable = [
        'menu_item_id',
        'quantity',
        'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /** unit_price x quantity, using the price captured at order time. */
    protected function lineTotal(): Attribute
    {
        return Attribute::get(fn () => round((float) $this->unit_price * $this->quantity, 2));
    }
}