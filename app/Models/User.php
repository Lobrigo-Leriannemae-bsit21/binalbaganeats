<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * SECURITY: "role" and "status" are intentionally NOT fillable, so they can
     * never be set through request data. Code must assign them explicitly.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'preferred_delivery_area',
        'gcash_number',
        'gcash_name',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    // ---- Relationships ----

    public function vendorProfile(): HasOne
    {
        return $this->hasOne(VendorProfile::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'vendor_id');
    }

    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function vendorOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'vendor_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'rider_id');
    }

    public function sukiPointTransactions(): HasMany
    {
        return $this->hasMany(SukiPointTransaction::class, 'customer_id');
    }

    public function buyForMeRequests(): HasMany
    {
        return $this->hasMany(BuyForMeRequest::class, 'customer_id');
    }

    public function assignedBuyForMeRequests(): HasMany
    {
        return $this->hasMany(BuyForMeRequest::class, 'rider_id');
    }

    /** Delivery zones (barangays) this rider serves. */
    public function serviceZones(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryZone::class, 'rider_service_zones', 'rider_id', 'delivery_zone_id')
            ->withTimestamps();
    }

    // ---- Scopes ----

    public function scopeOfRole(Builder $query, UserRole $role): Builder
    {
        return $query->where('role', $role->value);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Approved->value);
    }

    // ---- Helpers ----

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isVendor(): bool
    {
        return $this->role === UserRole::Vendor;
    }

    public function isRider(): bool
    {
        return $this->role === UserRole::Rider;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isApproved(): bool
    {
        return $this->status === UserStatus::Approved;
    }

    /** Up to two uppercase initials, used for the avatar circle. */
    protected function initials(): Attribute
    {
        return Attribute::get(fn () => collect(explode(' ', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode(''));
    }
}