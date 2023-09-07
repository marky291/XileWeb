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
        if (app()->runningUnitTests()) {
            Schema::create('game_woe_scores', function (Blueprint $table) {
                $table->id();
                $table->integer('season')->default(1);
                $table->integer('guild_id');
                $table->integer('guild_score')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_woe_scores');
    }
};
