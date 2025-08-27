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
        // Migrate existing data before dropping columns
        
        // Migrate blurb to patcher_notice where patcher_notice is null/empty
        DB::statement("UPDATE posts SET patcher_notice = blurb WHERE (patcher_notice IS NULL OR patcher_notice = '') AND blurb IS NOT NULL AND blurb != ''");
        
        // Migrate body to article_content where article_content is null/empty  
        DB::statement("UPDATE posts SET article_content = body WHERE (article_content IS NULL OR article_content = '') AND body IS NOT NULL AND body != ''");
        
        // Drop the unused columns
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['blurb', 'body']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the columns as nullable (data may be lost)
        Schema::table('posts', function (Blueprint $table) {
            $table->text('blurb')->nullable();
            $table->longText('body')->nullable();
        });
    }
};
