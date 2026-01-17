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
        Schema::table('uber_shop_categories', function (Blueprint $table) {
            $table->boolean('is_xilero')->default(true)->after('enabled');
            $table->boolean('is_xileretro')->default(false)->after('is_xilero');
        });

        // Mark all existing categories as XileRO only
        DB::table('uber_shop_categories')->update([
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);

        // Mark all existing items as XileRO only
        DB::table('uber_shop_items')->update([
            'is_xilero' => true,
            'is_xileretro' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uber_shop_categories', function (Blueprint $table) {
            $table->dropColumn(['is_xilero', 'is_xileretro']);
        });
    }
};
