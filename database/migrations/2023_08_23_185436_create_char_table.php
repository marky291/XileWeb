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
        if (app()->runningUnitTests())
        {
            Schema::create('char', function (Blueprint $table) {
                $table->increments('char_id');
                $table->unsignedInteger('account_id')->default(0)->index('account_id');
                $table->boolean('char_num')->default(false);
                $table->string('name', 30)->default('')->unique('name_key');
                $table->unsignedSmallInteger('class')->default(0);
                $table->unsignedSmallInteger('base_level')->default(1);
                $table->unsignedSmallInteger('job_level')->default(1);
                $table->unsignedBigInteger('base_exp')->default(0);
                $table->unsignedBigInteger('job_exp')->default(0);
                $table->unsignedInteger('zeny')->default(0);
                $table->unsignedSmallInteger('str')->default(0);
                $table->unsignedSmallInteger('agi')->default(0);
                $table->unsignedSmallInteger('vit')->default(0);
                $table->unsignedSmallInteger('int')->default(0);
                $table->unsignedSmallInteger('dex')->default(0);
                $table->unsignedSmallInteger('luk')->default(0);
                $table->unsignedInteger('max_hp')->default(0);
                $table->unsignedInteger('hp')->default(0);
                $table->unsignedInteger('max_sp')->default(0);
                $table->unsignedInteger('sp')->default(0);
                $table->unsignedInteger('status_point')->default(0);
                $table->unsignedInteger('skill_point')->default(0);
                $table->integer('option')->default(0);
                $table->tinyInteger('karma')->default(0);
                $table->smallInteger('manner')->default(0);
                $table->unsignedInteger('party_id')->default(0)->index('party_id');
                $table->unsignedInteger('guild_id')->default(0)->index('guild_id');
                $table->unsignedInteger('pet_id')->default(0);
                $table->unsignedInteger('homun_id')->default(0);
                $table->unsignedInteger('elemental_id')->default(0);
                $table->unsignedTinyInteger('hair')->default(0);
                $table->unsignedSmallInteger('hair_color')->default(0);
                $table->unsignedSmallInteger('clothes_color')->default(0);
                $table->unsignedSmallInteger('body')->default(0);
                $table->unsignedSmallInteger('weapon')->default(0);
                $table->unsignedSmallInteger('shield')->default(0);
                $table->unsignedSmallInteger('head_top')->default(0);
                $table->unsignedSmallInteger('head_mid')->default(0);
                $table->unsignedSmallInteger('head_bottom')->default(0);
                $table->unsignedSmallInteger('robe')->default(0);
                $table->string('last_map', 11)->default('');
                $table->unsignedSmallInteger('last_x')->default(53);
                $table->unsignedSmallInteger('last_y')->default(111);
                $table->string('save_map', 11)->default('');
                $table->unsignedSmallInteger('save_x')->default(53);
                $table->unsignedSmallInteger('save_y')->default(111);
                $table->unsignedInteger('partner_id')->default(0);
                $table->tinyInteger('online')->default(0)->index('online');
                $table->unsignedInteger('father')->default(0);
                $table->unsignedInteger('mother')->default(0);
                $table->unsignedInteger('child')->default(0);
                $table->unsignedInteger('fame')->default(0);
                $table->unsignedSmallInteger('rename')->default(0);
                $table->unsignedInteger('delete_date')->default(0);
                $table->unsignedInteger('moves')->default(0);
                $table->unsignedInteger('unban_time')->default(0);
                $table->unsignedTinyInteger('font')->default(0);
                $table->unsignedInteger('uniqueitem_counter')->default(0);
                $table->enum('sex', ['M', 'F']);
                $table->unsignedTinyInteger('hotkey_rowshift')->default(0);
                $table->unsignedTinyInteger('hotkey_rowshift2')->default(0);
                $table->unsignedInteger('clan_id')->default(0);
                $table->dateTime('last_login')->nullable();
                $table->unsignedInteger('title_id')->default(0);
                $table->unsignedTinyInteger('show_equip')->default(0);
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
        Schema::connection('main')->dropIfExists('char');
    }
};
