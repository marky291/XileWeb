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
        // Update all patches to x9
        DB::table('patches')->update(['client' => 'x9']);
        
        // Update all posts to x9
        DB::table('posts')->update(['client' => 'x9']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed as we're standardizing on x9
    }
};
