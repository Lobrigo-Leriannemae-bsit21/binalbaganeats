<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riders receive the customer's advance payment on their own GCash account.
        Schema::table('users', function (Blueprint $table) {
            $table->string('gcash_number', 25)->nullable()->after('preferred_delivery_area');
            $table->string('gcash_name')->nullable()->after('gcash_number');
        });

        // Which barangays (delivery zones) each rider serves. Decides which riders can accept a request.
        Schema::create('rider_service_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('delivery_zone_id')->constrained('delivery_zones')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['rider_id', 'delivery_zone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_service_zones');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gcash_number', 'gcash_name']);
        });
    }
};