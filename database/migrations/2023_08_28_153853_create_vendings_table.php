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
            Schema::create('vendings', function (Blueprint $table) {
                $table->unsignedInteger('id')->primary();
                $table->unsignedInteger('account_id');
                $table->unsignedInteger('char_id');
                $table->enum('sex', ['F', 'M'])->default('M');
                $table->string('map', 20);
                $table->unsignedSmallInteger('x');
                $table->unsignedSmallInteger('y');
                $table->string('title', 80);
                $table->char('body_direction', 1)->default('4');
                $table->char('head_direction', 1)->default('0');
                $table->char('sit', 1)->default('1');
                $table->tinyInteger('autotrade');
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
        Schema::connection('main')->dropIfExists('vendings');
    }
};
