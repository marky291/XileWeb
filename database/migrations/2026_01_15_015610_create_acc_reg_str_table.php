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
        Schema::create('acc_reg_str', function (Blueprint $table) {
            $table->unsignedInteger('account_id');
            $table->string('key', 32);
            $table->unsignedInteger('index')->default(0);
            $table->string('value', 255);

            $table->primary(['account_id', 'key', 'index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_reg_str');
    }
};
