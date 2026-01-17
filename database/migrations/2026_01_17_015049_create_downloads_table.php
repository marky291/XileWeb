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
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // full, android
            $table->string('link')->nullable();
            $table->string('file')->nullable();
            $table->string('file_name')->nullable();
            $table->string('version')->nullable();
            $table->string('button_style')->default('primary'); // primary, secondary
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
