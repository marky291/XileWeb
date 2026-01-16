<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('game_woe_events');
        Schema::dropIfExists('game_woe_scores');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tables intentionally not recreated - WoE feature has been removed
    }
};
