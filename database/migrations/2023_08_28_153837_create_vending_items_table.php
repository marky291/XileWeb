<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (app()->runningUnitTests()) {
            Schema::create('vending_items', function (Blueprint $table) {
                $table->unsignedInteger('vending_id');
                $table->unsignedSmallInteger('index');
                $table->unsignedInteger('cartinventory_id');
                $table->unsignedSmallInteger('amount');
                $table->unsignedInteger('price');

                $table->primary(['vending_id', 'index']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('main')->dropIfExists('vending_items');
    }
};
