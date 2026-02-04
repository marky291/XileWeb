<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uber_shop_purchases', function (Blueprint $table) {
            // Zeny Uber tracking columns
            // uber_cost = Donate Ubers spent
            // zeny_uber_cost = Zeny Ubers spent
            // Total cost = uber_cost + zeny_uber_cost
            $table->integer('zeny_uber_cost')->default(0)->after('uber_cost');
            $table->integer('zeny_uber_balance_after')->default(0)->after('uber_balance_after');
        });
    }

    public function down(): void
    {
        Schema::table('uber_shop_purchases', function (Blueprint $table) {
            $table->dropColumn(['zeny_uber_cost', 'zeny_uber_balance_after']);
        });
    }
};
