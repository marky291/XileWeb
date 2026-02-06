<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donation_reward_tiers', function (Blueprint $table) {
            $table->string('claim_reset_period')->nullable()->after('is_cumulative')
                ->comment('Cooldown period before tier can be claimed again: daily, weekly, monthly, yearly, or null for one-time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donation_reward_tiers', function (Blueprint $table) {
            $table->dropColumn('claim_reset_period');
        });
    }
};
