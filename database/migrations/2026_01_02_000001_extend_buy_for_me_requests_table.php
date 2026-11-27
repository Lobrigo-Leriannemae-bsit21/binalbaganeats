<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Customers no longer enter a maximum budget. The column is kept (nullable) so nothing is lost.
        Schema::table('buy_for_me_requests', function (Blueprint $table) {
            $table->decimal('maximum_budget', 10, 2)->nullable()->change();
        });

        Schema::table('buy_for_me_requests', function (Blueprint $table) {
            $table->decimal('estimated_expense', 10, 2)->nullable()->after('actual_expense');
            $table->decimal('delivery_fee', 8, 2)->nullable()->after('estimated_expense');
            $table->decimal('advance_amount', 10, 2)->nullable()->after('delivery_fee');
            $table->timestamp('advance_confirmed_at')->nullable()->after('advance_amount');
            $table->string('gcash_reference_number', 100)->nullable()->unique()->after('advance_confirmed_at');
            $table->string('advance_proof')->nullable()->after('gcash_reference_number');
            $table->string('receipt_image')->nullable()->after('advance_proof');
        });
    }

    public function down(): void
    {
        Schema::table('buy_for_me_requests', function (Blueprint $table) {
            $table->dropUnique(['gcash_reference_number']);
            $table->dropColumn([
                'estimated_expense',
                'delivery_fee',
                'advance_amount',
                'advance_confirmed_at',
                'gcash_reference_number',
                'advance_proof',
                'receipt_image',
            ]);
        });

        // maximum_budget is intentionally left nullable on rollback (making it required again could fail on existing rows).
    }
};