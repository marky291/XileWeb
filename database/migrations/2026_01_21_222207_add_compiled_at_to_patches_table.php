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
            $table->boolean('is_compiling')->default(false)->after('comments');
            $table->timestamp('compiled_at')->nullable()->after('is_compiling');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patches', function (Blueprint $table) {
            $table->dropColumn(['is_compiling', 'compiled_at']);
        });
    }
};
