<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class VendorSetStatus extends Command
{
    protected $signature = 'vendors:set-status
        {email : The vendor\'s email address}
        {status : approved, pending or rejected}';

    protected $description = 'DEVELOPMENT ONLY: approve, reject or reset a vendor application (the admin screen replaces this in Phase 4)';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('This development command is disabled in production.');

            return self::FAILURE;
        }

        $status = UserStatus::tryFrom(Str::lower((string) $this->argument('status')));

        if (! $status) {
            $this->error('Status must be approved, pending or rejected.');

            return self::FAILURE;
        }

        $vendor = User::query()
            ->where('email', Str::lower(trim((string) $this->argument('email'))))
            ->where('role', UserRole::Vendor->value)
            ->first();

        if (! $vendor) {
            $this->error('No vendor account was found with that email.');

            return self::FAILURE;
        }

        $vendor->status = $status;
        $vendor->save();

        $this->info("{$vendor->email} is now {$status->label()}.");

        return self::SUCCESS;
    }
}