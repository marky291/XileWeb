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
            $table->unique(['client', 'number'], 'patches_client_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patches', function (Blueprint $table) {
            $table->dropUnique('patches_client_number_unique');
        });
    }
};
