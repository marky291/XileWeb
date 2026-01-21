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
            Schema::create('guild', function (Blueprint $table) {
                $table->id('guild_id');
                $table->string('name', 24)->default('');
                $table->unsignedInteger('char_id')->default(0)->index('guild_char_id');
                $table->string('master', 24)->default('');
                $table->unsignedTinyInteger('guild_lv')->default(0);
                $table->unsignedTinyInteger('connect_member')->default(0);
                $table->unsignedTinyInteger('max_member')->default(0);
                $table->unsignedSmallInteger('average_lv')->default(1);
                $table->unsignedBigInteger('exp')->default(0);
                $table->unsignedBigInteger('next_exp')->default(0);
                $table->unsignedTinyInteger('skill_point')->default(0);
                $table->string('mes1', 60)->default('');
                $table->string('mes2', 120)->default('');
                $table->unsignedInteger('emblem_len')->default(0);
                $table->unsignedInteger('emblem_id')->default(0);
                $table->binary('emblem_data')->nullable();
                $table->dateTime('last_master_change')->nullable();
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
        Schema::connection('main')->dropIfExists('guild');
    }
};
