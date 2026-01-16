<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uber_shop_categories', function (Blueprint $table) {
            $table->dropColumn(['is_xilero', 'is_xileretro']);
        });
    }

    public function down(): void
    {
        Schema::table('uber_shop_categories', function (Blueprint $table) {
            $table->boolean('is_xilero')->default(true)->after('enabled');
            $table->boolean('is_xileretro')->default(false)->after('is_xilero');
        });
    }
};
