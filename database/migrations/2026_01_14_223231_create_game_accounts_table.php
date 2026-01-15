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
        Schema::create('game_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('ragnarok_account_id')->nullable()->unique();
            $table->string('userid', 23)->unique();
            $table->string('user_pass', 128);
            $table->string('email', 39);
            $table->char('sex', 1)->default('M');
            $table->unsignedTinyInteger('group_id')->default(0);
            $table->unsignedTinyInteger('state')->default(0);
            $table->unsignedInteger('uber_balance')->default(0);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_accounts');
    }
};
