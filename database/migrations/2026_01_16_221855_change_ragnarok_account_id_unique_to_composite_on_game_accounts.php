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
        Schema::table('game_accounts', function (Blueprint $table) {
            // Drop the single-column unique constraint
            $table->dropUnique(['ragnarok_account_id']);

            // Add composite unique constraint (ragnarok_account_id + server)
            $table->unique(['ragnarok_account_id', 'server'], 'game_accounts_ragnarok_server_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_accounts', function (Blueprint $table) {
            // Drop composite unique constraint
            $table->dropUnique('game_accounts_ragnarok_server_unique');

            // Restore single-column unique constraint
            $table->unique('ragnarok_account_id');
        });
    }
};
