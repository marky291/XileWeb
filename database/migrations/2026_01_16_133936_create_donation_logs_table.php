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
        Schema::create('donation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->comment('Donation amount in USD');
            $table->string('payment_method');
            $table->integer('base_ubers')->comment('Ubers before bonus');
            $table->integer('bonus_ubers')->default(0)->comment('Bonus ubers (crypto bonus + admin generosity)');
            $table->integer('total_ubers')->comment('Total ubers applied');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_logs');
    }
};
