<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('business_name');
            $table->string('business_address');
            $table->text('description')->nullable();
            $table->string('vendor_image')->nullable();
            $table->unsignedInteger('estimated_prep_time')->default(15); // in minutes
            $table->string('gcash_number', 25)->nullable();
            $table->string('gcash_name')->nullable();
            $table->boolean('is_available')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_profiles');
    }
};