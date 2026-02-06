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
        Schema::create('donation_reward_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donation_log_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('donation_reward_tier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->integer('refine_level')->default(0);
            $table->enum('status', ['pending', 'claimed', 'expired'])->default('pending');
            $table->timestamp('claimed_at')->nullable();
            $table->unsignedBigInteger('claimed_account_id')->nullable();
            $table->string('claimed_char_name')->nullable();
            $table->boolean('is_xilero')->default(true);
            $table->boolean('is_xileretro')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('donation_reward_tier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_reward_claims');
    }
};
