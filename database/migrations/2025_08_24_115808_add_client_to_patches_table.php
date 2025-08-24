<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patches', function (Blueprint $table) {
            $table->enum('client', ['retro', 'xilero'])->default('xilero')->after('id');
        });

        // Update all existing records to use 'retro'
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
