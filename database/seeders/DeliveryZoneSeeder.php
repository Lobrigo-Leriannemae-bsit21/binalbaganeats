<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

/**
 * SAMPLE delivery zones so checkout can be tested.
 *
 * The zone names, distances and especially the FEES AND TRAVEL TIMES BELOW ARE
 * PLACEHOLDERS, not your real prices. Replace them with your real zones (edit the
 * delivery_zones table in phpMyAdmin until the Admin app arrives in Phase 4).
 *
 * Safe to run more than once: zones that already exist (by name) are never changed.
 *
 * Run with:  php artisan db:seed --class=DeliveryZoneSeeder
 */
class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            // name, distance range, fee (PHP), travel time (minutes)
            ['Zone 1 - Brgy. San Pedro & Poblacion Core', '0-2 km', 30.00, 15],
            ['Zone 2 - Nearby Barangays', '2-5 km', 50.00, 25],
            ['Zone 3 - Outer Barangays', '5-10 km', 80.00, 40],
        ];

        foreach ($zones as [$name, $range, $fee, $minutes]) {
            DeliveryZone::firstOrCreate(
                ['zone_name' => $name],
                [
                    'distance_range' => $range,
                    'delivery_fee' => $fee,
                    'estimated_travel_time' => $minutes,
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info('Sample delivery zones are ready (placeholder fees: replace them with your real ones).');
    }
}