<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buy_for_me_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 30)->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('delivery_zone_id')->constrained('delivery_zones')->restrictOnDelete();
            $table->foreignId('rider_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('establishment_name');
            $table->text('items_summary');
            $table->string('delivery_address');
            $table->decimal('maximum_budget', 10, 2);
            $table->decimal('actual_expense', 10, 2)->nullable();
            $table->string('request_status', 50)->default('Open Request');
            $table->string('settlement_status', 60)->default('Unassigned');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buy_for_me_requests');
    }
};