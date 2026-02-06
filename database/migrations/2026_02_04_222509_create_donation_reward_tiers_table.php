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
        Schema::create('donation_reward_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('minimum_amount', 10, 2);
            $table->boolean('is_cumulative')->default(false)->comment('Whether lower tiers also trigger');
            $table->enum('trigger_type', ['per_donation', 'lifetime'])->default('per_donation');
            $table->boolean('is_xilero')->default(true);
            $table->boolean('is_xileretro')->default(true);
            $table->boolean('enabled')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['enabled', 'minimum_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_reward_tiers');
    }
};
