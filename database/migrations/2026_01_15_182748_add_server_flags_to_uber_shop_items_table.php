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
        Schema::table('uber_shop_items', function (Blueprint $table) {
            $table->boolean('is_xilero')->default(true)->after('enabled');
            $table->boolean('is_xileretro')->default(false)->after('is_xilero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uber_shop_items', function (Blueprint $table) {
            $table->dropColumn(['is_xilero', 'is_xileretro']);
        });
    }
};
