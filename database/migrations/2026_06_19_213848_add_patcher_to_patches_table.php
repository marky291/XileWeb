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
        Schema::table('patches', function (Blueprint $table) {
            // 'legacy'   = historical .gpf patches for the old Thor patcher
            // 'rpatchur' = .thor/.grf patches served to the modern rpatchur launcher
            $table->string('patcher')->default('legacy')->after('client')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patches', function (Blueprint $table) {
            $table->dropIndex(['patcher']);
            $table->dropColumn('patcher');
        });
    }
};
