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
        Schema::table('donation_logs', function (Blueprint $table) {
            $table->timestamp('reverted_at')->nullable();
            $table->foreignId('reverted_by')->nullable()->constrained('users');
            $table->integer('ubers_recovered')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donation_logs', function (Blueprint $table) {
            $table->dropForeign(['reverted_by']);
            $table->dropColumn(['reverted_at', 'reverted_by', 'ubers_recovered']);
        });
    }
};
