<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\DeliveryZone;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * DEVELOPMENT ONLY. Creates one clearly labelled sample rider who serves every active
 * barangay and has a (fake) GCash account, so Buy-for-Me can be tested.
 * Safe to run more than once. Sample rider password: Password1
 *
 * Run with:  php artisan db:seed --class=SampleRiderSeeder
 */
class SampleRiderSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Sample data is not allowed in production.');

            return;
        }

        $rider = User::firstOrNew(['email' => 'rider@sample.binalbaganeats.test']);

        if (! $rider->exists) {
            // role / status are not mass-assignable, so they are set explicitly.
            $rider->forceFill([
                'name' => 'Sample Rider',
                'phone' => '09179990000',
                'address' => 'Binalbagan, Negros Occidental',
                'role' => UserRole::Rider,
                'status' => UserStatus::Approved,
                'gcash_number' => '09179990000',
                'gcash_name' => 'Sample Rider',
                'password' => 'Password1',
            ])->save();
        }

        $rider->serviceZones()->syncWithoutDetaching(DeliveryZone::query()->active()->pluck('id')->all());

        $this->command?->info('Sample rider is ready (rider@sample.binalbaganeats.test, serves '.$rider->serviceZones()->count().' zones).');
    }
}