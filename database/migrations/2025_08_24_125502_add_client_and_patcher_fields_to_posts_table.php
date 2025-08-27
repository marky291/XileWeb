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
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('client', ['retro', 'xilero'])->default('xilero')->after('id');
            $table->text('patcher_notice')->nullable()->after('client');
            $table->longText('article_content')->nullable()->after('patcher_notice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['client', 'patcher_notice', 'article_content']);
        });
    }
};
