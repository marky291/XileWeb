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
        if (app()->runningUnitTests())
        {
            Schema::create('server_zeny', function (Blueprint $table) {
                $table->integer('silver_count')->default(0);
                $table->integer('gold_count')->default(0);
                $table->integer('mithril_count')->default(0);
                $table->integer('platinum_count')->default(0);
                $table->integer('player_zeny')->default(0);
                $table->integer('char_online')->default(0);
                $table->integer('silver_zeny')->default(0);
                $table->integer('gold_zeny')->default(0);
                $table->integer('mithril_zeny')->default(0);
                $table->integer('platinum_zeny')->default(0);
                $table->integer('total_zeny')->default(0);
                $table->integer('total_uber_cost')->default(0);
                $table->integer('mithril_cost')->default(0);
                $table->integer('platinum_cost')->default(0);
                $table->integer('gold_cost')->default(0);
                $table->integer('silver_cost')->default(0);
                $table->integer('zeny_cost')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_zeny');
    }
};
