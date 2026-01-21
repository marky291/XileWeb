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
            Schema::create('inventory', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('char_id')->default(0)->index('inventory_char_id');
                $table->unsignedInteger('nameid')->default(0);
                $table->unsignedInteger('amount')->default(0);
                $table->unsignedInteger('equip')->default(0);
                $table->unsignedTinyInteger('identify')->default(0);
                $table->unsignedTinyInteger('refine')->default(0);
                $table->unsignedTinyInteger('attribute')->default(0);
                $table->unsignedSmallInteger('card0')->default(0);
                $table->unsignedSmallInteger('card1')->default(0);
                $table->unsignedSmallInteger('card2')->default(0);
                $table->unsignedSmallInteger('card3')->default(0);
                $table->unsignedSmallInteger('option_id0')->default(0);
                $table->unsignedSmallInteger('option_val0')->default(0);
                $table->unsignedTinyInteger('option_parm0')->default(0);
                $table->unsignedSmallInteger('option_id1')->default(0);
                $table->unsignedSmallInteger('option_val1')->default(0);
                $table->unsignedTinyInteger('option_parm1')->default(0);
                $table->unsignedSmallInteger('option_id2')->default(0);
                $table->unsignedSmallInteger('option_val2')->default(0);
                $table->unsignedTinyInteger('option_parm2')->default(0);
                $table->unsignedSmallInteger('option_id3')->default(0);
                $table->unsignedSmallInteger('option_val3')->default(0);
                $table->unsignedTinyInteger('option_parm3')->default(0);
                $table->unsignedSmallInteger('option_id4')->default(0);
                $table->unsignedSmallInteger('option_val4')->default(0);
                $table->unsignedTinyInteger('option_parm4')->default(0);
                $table->unsignedInteger('expire_time')->default(0);
                $table->unsignedTinyInteger('favorite')->default(0);
                $table->unsignedTinyInteger('bound')->default(0);
                $table->unsignedBigInteger('unique_id')->default(0);
                $table->unsignedInteger('equip_switch')->default(0);
                $table->unsignedTinyInteger('enchantgrade')->default(0);
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
        Schema::dropIfExists('inventory');
    }
};
