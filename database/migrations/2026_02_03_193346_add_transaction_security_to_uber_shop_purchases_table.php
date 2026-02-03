<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add transaction security columns.
     *
     * - txn_token: Prevents duplicate purchase records from rapid clicks
     * - claim_token: Prevents duplicate item delivery during redemption
     */
    public function up(): void
    {
        Schema::table('uber_shop_purchases', function (Blueprint $table) {
            $table->string('txn_token', 100)->nullable()->after('is_xileretro');
            $table->string('claim_token', 100)->nullable()->after('txn_token');

            $table->unique('txn_token', 'uk_txn_token');
            $table->index('claim_token', 'idx_claim_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uber_shop_purchases', function (Blueprint $table) {
            $table->dropUnique('uk_txn_token');
            $table->dropIndex('idx_claim_token');
            $table->dropColumn(['txn_token', 'claim_token']);
        });
    }
};
