<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('database_items');
    }

    public function down(): void
    {
        // Table has been replaced by 'items' table - no rollback needed
    }
};
