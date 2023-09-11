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
            Schema::create('game_woe_events', function (Blueprint $table) {
                $table->increments('id');
                $table->string('message', 500)->nullable();
                $table->string('castle', 50)->nullable();
                $table->integer('edition')->nullable();
                $table->integer('season')->default(1);
                $table->string('event')->nullable();
                $table->integer('guild_id')->nullable();
                $table->integer('player')->nullable();
                $table->tinyInteger('discord_sent')->nullable()->default(0);
                $table->tinyInteger('processed')->nullable();
                $table->timestamp('updated_at')->nullable()->useCurrent();
                $table->timestamp('created_at')->nullable()->useCurrent();
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
        Schema::dropIfExists('game_woe_events');
    }
};
