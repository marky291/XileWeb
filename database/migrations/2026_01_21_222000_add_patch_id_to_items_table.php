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
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('data_patch_id')->nullable()->after('is_xileretro')->constrained('patches')->nullOnDelete();
            $table->foreignId('sprite_patch_id')->nullable()->after('data_patch_id')->constrained('patches')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('data_patch_id');
            $table->dropConstrainedForeignId('sprite_patch_id');
        });
    }
};
