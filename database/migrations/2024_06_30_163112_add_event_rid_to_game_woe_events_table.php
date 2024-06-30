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
            Schema::table('game_woe_events', function (Blueprint $table) {
                $table->string('event_rid')->nullable()->after('event');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_woe_events', function (Blueprint $table) {
            //
        });
    }
};
