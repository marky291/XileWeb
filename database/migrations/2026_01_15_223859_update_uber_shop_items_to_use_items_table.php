<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uber_shop_items', function (Blueprint $table) {
            // Drop columns now available on items table
            $table->dropColumn([
                'item_name',
                'aegis_name',
                'display_name',
                'description',
                'item_type',
                'item_subtype',
                'icon_path',
                'collection_path',
                'slots',
                'weight',
                'equip_locations',
            ]);
        });

        // Change item_id to match items.id type (bigint unsigned)
        Schema::table('uber_shop_items', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->change();
        });

        // Add foreign key constraint
        Schema::table('uber_shop_items', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('uber_shop_items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);

            // Restore original columns
            $table->string('item_name')->after('item_id');
            $table->string('aegis_name')->nullable()->after('item_name');
            $table->string('display_name')->nullable()->after('aegis_name');
            $table->text('description')->nullable()->after('display_name');
            $table->string('item_type')->nullable()->after('description');
            $table->string('item_subtype')->nullable()->after('item_type');
            $table->string('icon_path')->nullable()->after('item_subtype');
            $table->string('collection_path')->nullable()->after('icon_path');
            $table->integer('slots')->default(0)->after('refine_level');
            $table->integer('weight')->default(0)->after('slots');
            $table->string('equip_locations')->nullable()->after('weight');
        });
    }
};
