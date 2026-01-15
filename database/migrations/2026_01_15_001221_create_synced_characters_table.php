<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('synced_characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_account_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('char_id')->index();
            $table->string('name');
            $table->string('class_name');
            $table->unsignedSmallInteger('base_level')->default(1);
            $table->unsignedSmallInteger('job_level')->default(1);
            $table->unsignedBigInteger('zeny')->default(0);
            $table->string('last_map')->nullable();
            $table->string('guild_name')->nullable();
            $table->boolean('online')->default(false);
            $table->timestamp('synced_at');
            $table->timestamps();

            $table->unique(['game_account_id', 'char_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('synced_characters');
    }
};
