<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patches', function (Blueprint $table) {
            $table->enum('client', ['retro', 'x9'])->default('x9')->after('type');
        });

        // Update existing patches to 'retro' for backward compatibility
        DB::table('patches')->update(['client' => 'retro']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patches', function (Blueprint $table) {
            $table->dropColumn('client');
        });
    }
};
