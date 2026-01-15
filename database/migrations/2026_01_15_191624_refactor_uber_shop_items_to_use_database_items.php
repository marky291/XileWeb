<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uber_shop_items', function (Blueprint $table) {
            // Add reference to database_items
            $table->foreignId('database_item_id')
                ->nullable()
                ->after('category_id')
                ->constrained('database_items')
                ->nullOnDelete();

            // Remove columns that are now on database_items
            $table->dropColumn([
                'item_id',
                'item_name',
                'aegis_name',
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
    }

    public function down(): void
    {
        Schema::table('uber_shop_items', function (Blueprint $table) {
            // Remove foreign key
            $table->dropForeign(['database_item_id']);
            $table->dropColumn('database_item_id');

            // Restore original columns
            $table->integer('item_id')->after('category_id');
            $table->string('item_name')->after('item_id');
            $table->string('aegis_name')->nullable()->after('item_name');
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
