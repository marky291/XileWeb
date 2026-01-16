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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('item_id');
            $table->string('aegis_name')->default('');
            $table->string('name')->default('');
            $table->text('description')->nullable();
            $table->string('type', 50)->default('Etc');
            $table->string('subtype', 50)->nullable();
            $table->unsignedInteger('weight')->default(0);
            $table->unsignedInteger('buy')->default(0);
            $table->unsignedInteger('sell')->default(0);
            $table->unsignedInteger('attack')->default(0);
            $table->unsignedInteger('defense')->default(0);
            $table->unsignedTinyInteger('slots')->default(0);
            $table->boolean('refineable')->default(false);
            $table->json('jobs')->nullable();
            $table->json('locations')->nullable();
            $table->json('flags')->nullable();
            $table->json('trade')->nullable();
            $table->text('script')->nullable();
            $table->text('equip_script')->nullable();
            $table->text('unequip_script')->nullable();
            $table->boolean('is_xileretro')->default(false)->comment('0=XileRO, 1=XileRetro');
            $table->timestamps();

            $table->unique(['item_id', 'is_xileretro'], 'idx_item_server');
            $table->index('item_id', 'idx_item_id');
            $table->index('aegis_name', 'idx_aegis_name');
            $table->index('name', 'idx_name');
            $table->index('type', 'idx_type');
            $table->index('is_xileretro', 'idx_is_xileretro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
