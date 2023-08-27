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
        if (app()->runningUnitTests()) {
            Schema::create('guild_castle', function (Blueprint $table) {
                $table->integer('castle_id');
                $table->integer('guild_id');
                $table->integer('economy');
                $table->integer('defense');
                $table->integer('triggerE');
                $table->integer('triggerD');
                $table->integer('nextTime');
                $table->integer('payTime');
                $table->integer('createTime');
                $table->integer('visibleC');
                $table->integer('visibleG0');
                $table->integer('visibleG1');
                $table->integer('visibleG2');
                $table->integer('visibleG3');
                $table->integer('visibleG4');
                $table->integer('visibleG5');
                $table->integer('visibleG6');
                $table->integer('visibleG7');
                $table->primary('castle_id');
                $table->index('guild_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guild_castle');
    }
};
