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
            Schema::create('donation_ubers', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('account_id');
                $table->string('username', 50);
                $table->unsignedInteger('current_ubers')->nullable()->default(0);
                $table->unsignedInteger('pending_ubers')->nullable()->default(0);
                $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
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
        Schema::connection('main')->dropIfExists('donation_ubers');
    }
};
