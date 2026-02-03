<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create uber_delivery_log table on XileRO game database.
     *
     * This table provides crash-safe delivery confirmation that
     * doesn't depend on web DB availability. Prevents item duplication.
     *
     * Skipped during testing to avoid connecting to game databases.
     */
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        Schema::connection('xilero_main')->create('uber_delivery_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('purchase_id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('char_id');
            $table->string('char_name', 30);
            $table->unsignedInteger('item_id');
            $table->string('item_name', 100);
            $table->unsignedTinyInteger('refine')->default(0);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamp('delivered_at');

            $table->unique('purchase_id', 'uk_purchase_id');
            $table->index('account_id', 'idx_account');
            $table->index('delivered_at', 'idx_delivered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        Schema::connection('xilero_main')->dropIfExists('uber_delivery_log');
    }
};
