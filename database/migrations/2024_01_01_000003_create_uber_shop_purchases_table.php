<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uber_shop_purchases', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id');
            $table->string('account_name');
            $table->foreignId('shop_item_id')->nullable()->constrained('uber_shop_items')->nullOnDelete();
            $table->integer('item_id');
            $table->string('item_name');
            $table->integer('refine_level')->default(0);
            $table->integer('quantity')->default(1);
            $table->integer('uber_cost');
            $table->integer('uber_balance_after');
            $table->string('status')->default('pending');
            $table->timestamp('purchased_at');
            $table->timestamp('claimed_at')->nullable();
            $table->integer('claimed_by_char_id')->nullable();
            $table->string('claimed_by_char_name')->nullable();
            $table->timestamps();

            $table->index('account_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uber_shop_purchases');
    }
};
