<?php

use App\Models\Patch;
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
        Schema::create('patches', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->string('type');
            $table->string('patch_name');
            $table->string('comments');
            $table->timestamps();
        });

        Patch::insert([
            ['number' => 324, 'type' => 'FLD', 'patch_name' => '18032022_LUBFix.gpf', 'comments' => 'New Lubs back into data folder'],
            ['number' => 325, 'type' => 'FLD', 'patch_name' => '01072022_AttendenceUpdate.gpf', 'comments' => 'attendance update'],
            ['number' => 326, 'type' => 'FLD', 'patch_name' => '01072022_UpdateMenuGraphics.gpf', 'comments' => 'background update'],
            ['number' => 327, 'type' => 'GRF', 'patch_name' => '01102022_OctoberPatch.gpf', 'comments' => 'Halloween Patch 1 to GRF (Encrypted)'],
            ['number' => 328, 'type' => 'GRF', 'patch_name' => '01102022_OctoberPatch2.gpf', 'comments' => 'Halloween Patch 2 to GRF (Encrypted)'],
            ['number' => 329, 'type' => 'FLD', 'patch_name' => '01102022_OctoberPatch3.gpf', 'comments' => 'Halloween Patch ItemInfo & Lua\'s (Not Encrypted)'],
            ['number' => 330, 'type' => 'GRF', 'patch_name' => '01102022_OctoberPatch5.gpf', 'comments' => 'Halloween Patch 2 to GRF (Encrypted)'],
            ['number' => 331, 'type' => 'FLD', 'patch_name' => '01102022_OctoberPatch4.gpf', 'comments' => 'Halloween Patch ItemInfo & Lua\'s (Not Encrypted)'],
            ['number' => 332, 'type' => 'GRF', 'patch_name' => '03102022_OctoberLogin.gpf', 'comments' => 'Halloween Login BG (Encrypted)'],
            ['number' => 333, 'type' => 'GRF', 'patch_name' => '07042023_April_Update.gpf', 'comments' => 'April to GRF (Encrypted)'],
            ['number' => 334, 'type' => 'FLD', 'patch_name' => '07042023_April_Update_II.gpf', 'comments' => 'April ItemInfo & Lua\'s (Not Encrypted)'],
            ['number' => 335, 'type' => 'FLD', 'patch_name' => '07042023_April_Update_III.gpf', 'comments' => 'April ItemInfo (Not Encrypted)'],
            ['number' => 336, 'type' => 'GRF', 'patch_name' => '30062023_June_Update.gpf', 'comments' => 'June to GRF (Encrypted)'],
            ['number' => 337, 'type' => 'FLD', 'patch_name' => '30062023_June_Update_II.gpf', 'comments' => 'June ItemInfo & Lua\'s (Not Encrypted)'],
            ['number' => 338, 'type' => 'FLD', 'patch_name' => '02072023_July_Update_ATD.gpf', 'comments' => 'July Attendance (Not Encrypted)'],
            ['number' => 339, 'type' => 'GRF', 'patch_name' => '01082023_LoginWallpaper.gpf', 'comments' => 'Mark'],
            ['number' => 340, 'type' => 'FLD', 'patch_name' => '05082023_August_Update_ATD_II.gpf', 'comments' => 'August Attendance (Not Encrypted)'],
            ['number' => 341, 'type' => 'GRF', 'patch_name' => '06082023_August_Update.gpf', 'comments' => 'August to GRF (Encrypted)'],
            ['number' => 345, 'type' => 'GRF', 'patch_name' => '11082023_GMSpritesGojo2.gpf', 'comments' => 'August to GRF (Encrypted)'],
            ['number' => 348, 'type' => 'FLD', 'patch_name' => '19082023_RookieBadgeSprite.gpf', 'comments' => ''],
            ['number' => 349, 'type' => 'FLD', 'patch_name' => '19082023_3vs3-5mffa-ArenaModes.gpf', 'comments' => ''],
            ['number' => 351, 'type' => 'FLD', 'patch_name' => '26082023_QuickFix.gpf', 'comments' => 'Item Info & Patcher Skin'],
            ['number' => 352, 'type' => 'FLD', 'patch_name' => '27082023_SClientInfo.gpf', 'comments' => 'Updated client entry text'],
            ['number' => 354, 'type' => 'FLD', 'patch_name' => '29082023_cardpostfix.gpf', 'comments' => 'card post fix'],
            ['number' => 355, 'type' => 'FLD', 'patch_name' => '02092023_Sept_ATD_II.gpf', 'comments' => 'Sept Assets (Not Encrypted)'],
            ['number' => 356, 'type' => 'GRF', 'patch_name' => '02092023_SeptAST.gpf', 'comments' => 'Sept Assets (Encrypted)'],
            ['number' => 358, 'type' => 'FLD', 'patch_name' => '02092023_PatchConfiguration.gpf', 'comments' => 'patch notice change']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patches');
    }
};
