<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class MenuItem extends Model
{
    // vendor_id is set through $vendor->menuItems()->create(...), never from input.
    protected $fillable = [
        'item_name',
        'category',
        'description',
        'price',
        'prep_time',
        'menu_image',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'prep_time' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ---- Scopes ----

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /** Items whose vendor is an approved vendor account with a store profile. */
    public function scopeFromVisibleVendors(Builder $query): Builder
    {
        return $query->whereHas('vendor', fn (Builder $q) => $q
            ->ofRole(UserRole::Vendor)
            ->approved()
            ->whereHas('vendorProfile'));
    }

    /** Available items from visible vendors whose store is currently open. */
    public function scopeOrderableNow(Builder $query): Builder
    {
        return $query->available()
            ->fromVisibleVendors()
            ->whereHas('vendor.vendorProfile', fn (Builder $p) => $p->where('is_available', true));
    }

    // ---- Helpers ----

    /**
     * True when a customer may order this item right now: the item is on,
     * and its vendor is approved with an open store.
     * (Eager-load "vendor.vendorProfile" when calling this in loops.)
     */
    public function isOrderable(): bool
    {
        $vendor = $this->vendor;
        $profile = $vendor?->vendorProfile;

        return $this->is_available
            && $vendor !== null
            && $profile !== null
            && $profile->is_available
            && $vendor->isVendor()
            && $vendor->isApproved();
    }

    /** Categories that currently have at least one available item from a visible vendor. */
    public static function activeCategories(): Collection
    {
        return static::sortCategories(
            static::query()->available()->fromVisibleVendors()->distinct()->pluck('category')
        );
    }

    /** Orders categories using config('binalbagan.category_order'), unknown ones last A-Z. */
    public static function sortCategories(Collection $categories): Collection
    {
        $order = config('binalbagan.category_order', []);

        return $categories
            ->unique()
            ->sortBy(function (string $category) use ($order) {
                $position = array_search($category, $order, true);

                return sprintf('%03d-%s', $position === false ? 999 : $position, mb_strtolower($category));
            })
            ->values();
    }

    public static function categoryIcon(string $category): string
    {
        return config('binalbagan.category_icons.'.$category, 'bi-egg-fried');
    }

    // ---- Accessors ----

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->menu_image
            ? asset('storage/'.$this->menu_image)
            : asset('images/placeholder-food.svg'));
    }
}