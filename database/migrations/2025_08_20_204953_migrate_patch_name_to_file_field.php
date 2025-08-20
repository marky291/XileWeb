<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing patch_name values to file field
        $patches = DB::table('patches')->whereNotNull('patch_name')->whereNull('file')->get();

        foreach ($patches as $patch) {
            // Convert patch_name to storage path format
            $filePath = 'patches/'.$patch->patch_name;

            DB::table('patches')
                ->where('id', $patch->id)
                ->update(['file' => $filePath]);
        }
    }

    public function down(): void
    {
        // Revert by clearing file field for patches that have patch_name
        $patches = DB::table('patches')->whereNotNull('patch_name')->whereNotNull('file')->get();

        foreach ($patches as $patch) {
            DB::table('patches')
                ->where('id', $patch->id)
                ->update(['file' => null]);
        }
    }
};
