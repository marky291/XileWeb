<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create uber_purchase_locks table.
     *
     * Prevents concurrent purchases from multiple game accounts
     * linked to the same master account.
     */
    public function up(): void
    {
        Schema::create('uber_purchase_locks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_account_id');
            $table->string('lock_token', 100);
            $table->timestamp('locked_at');
            $table->timestamp('expires_at');

            $table->unique('master_account_id', 'uk_master_account');
            $table->unique('lock_token', 'uk_lock_token');
            $table->index('expires_at', 'idx_expires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uber_purchase_locks');
    }
};
