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
        Schema::create('database_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('item_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('aegis_name')->nullable();
            $table->string('item_type')->nullable();
            $table->string('item_subtype')->nullable();
            $table->unsignedTinyInteger('slots')->default(0);
            $table->unsignedInteger('weight')->default(0);
            $table->unsignedInteger('attack')->default(0);
            $table->unsignedInteger('defense')->default(0);
            $table->unsignedSmallInteger('equip_level_min')->default(0);
            $table->unsignedTinyInteger('weapon_level')->default(0);
            $table->json('equip_locations')->nullable();
            $table->json('jobs')->nullable();
            $table->unsignedInteger('buy_price')->default(0);
            $table->unsignedInteger('sell_price')->default(0);
            $table->string('icon_path')->nullable();
            $table->string('collection_path')->nullable();
            $table->string('client_icon')->nullable();
            $table->string('client_collection')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('item_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_items');
    }
};
