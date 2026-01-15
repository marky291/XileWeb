<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uber_shop_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('uber_shop_categories')->nullOnDelete();
            $table->integer('item_id');
            $table->string('item_name');
            $table->string('aegis_name')->nullable();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->string('item_type')->nullable();
            $table->string('item_subtype')->nullable();
            $table->string('icon_path')->nullable();
            $table->string('collection_path')->nullable();
            $table->integer('uber_cost');
            $table->integer('quantity')->default(1);
            $table->integer('refine_level')->default(0);
            $table->integer('slots')->default(0);
            $table->integer('weight')->default(0);
            $table->string('equip_locations')->nullable();
            $table->integer('stock')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uber_shop_items');
    }
};
