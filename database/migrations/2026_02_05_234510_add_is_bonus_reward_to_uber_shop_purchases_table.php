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
        Schema::table('uber_shop_purchases', function (Blueprint $table) {
            $table->boolean('is_bonus_reward')->default(false)->after('is_xileretro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uber_shop_purchases', function (Blueprint $table) {
            $table->dropColumn('is_bonus_reward');
        });
    }
};
