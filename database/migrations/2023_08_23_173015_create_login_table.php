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
            Schema::create('login', function (Blueprint $table) {
                $table->increments('account_id');
                $table->string('userid', 23)->default('');
                $table->string('user_pass', 64)->default('');
                $table->enum('sex', ['M', 'F', 'S'])->default('M');
                $table->string('email', 39)->default('');
                $table->tinyInteger('group_id')->default(0);
                $table->unsignedInteger('state')->default(0);
                $table->unsignedInteger('unban_time')->default(0);
                $table->unsignedInteger('expiration_time')->default(0);
                $table->unsignedMediumInteger('logincount')->default(0);
                $table->dateTime('lastlogin')->nullable();
                $table->string('last_ip', 100)->default('');
                $table->date('birthdate')->nullable();
                $table->unsignedTinyInteger('character_slots')->default(0);
                $table->string('pincode', 4)->default('');
                $table->unsignedInteger('pincode_change')->default(0);
                $table->unsignedInteger('vip_time')->default(0);
                $table->tinyInteger('old_group')->default(0);
                $table->string('web_auth_token', 17)->nullable()->unique();
                $table->tinyInteger('web_auth_token_enabled')->default(0);
                $table->string('remember_token', 100)->nullable();
                $table->timestamp('email_verified_at')->nullable();

                $table->index('userid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login');
    }
};
