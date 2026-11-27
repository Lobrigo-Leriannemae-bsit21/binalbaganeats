<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\Search;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorProfile extends Model
{
    // user_id and approved_by are set in code (relationship / admin action), not by request data.
    protected $fillable = [
        'business_name',
        'business_address',
        'description',
        'vendor_image',
        'estimated_prep_time',
        'gcash_number',
        'gcash_name',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'estimated_prep_time' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** menu_items.vendor_id points at users.id, which is this profile's user_id. */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'vendor_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'vendor_id', 'user_id');
    }

    // ---- Scopes ----

    /** Vendors that are approved by an admin and currently open. */
    public function scopeOpenForOrders(Builder $query): Builder
    {
        return $query->where('is_available', true)
            ->whereHas('user', fn (Builder $q) => $q->approved());
    }

    /** Vendors customers are allowed to see: an approved account with the vendor role. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereHas('user', fn (Builder $q) => $q->ofRole(UserRole::Vendor)->approved());
    }

    public function scopeHasAvailableItems(Builder $query): Builder
    {
        return $query->whereHas('menuItems', fn (Builder $q) => $q->available());
    }

    public function scopeWithAvailableItemCount(Builder $query): Builder
    {
        return $query->withCount([
            'menuItems as available_items_count' => fn (Builder $q) => $q->where('is_available', true),
        ]);
    }

    /** Matches the store name, its description, or the name of any available dish. */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = Search::like($term);

        return $query->where(function (Builder $q) use ($like) {
            $q->where('business_name', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhereHas('menuItems', fn (Builder $m) => $m->available()->where('item_name', 'like', $like));
        });
    }

    public function scopeWithCategory(Builder $query, ?string $category): Builder
    {
        if ($category === null || $category === '') {
            return $query;
        }

        return $query->whereHas('menuItems', fn (Builder $m) => $m->available()->where('category', $category));
    }

    // ---- Accessors ----

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->vendor_image
            ? asset('storage/'.$this->vendor_image)
            : asset('images/placeholder-store.svg'));
    }
}