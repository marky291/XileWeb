<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patches', function (Blueprint $table) {
            // Add new file column if it doesn't exist
            if (! Schema::hasColumn('patches', 'file')) {
                $table->string('file')->nullable()->after('patch_name');
            }

            // Migrate data from files JSON column to file string column if needed
            if (Schema::hasColumn('patches', 'files')) {
                // Migrate existing data
                $patches = DB::table('patches')->whereNotNull('files')->get();
                foreach ($patches as $patch) {
                    $files = json_decode($patch->files, true);
                    if (! empty($files) && is_array($files)) {
                        DB::table('patches')
                            ->where('id', $patch->id)
                            ->update(['file' => $files[0]]);
                    }
                }

                // Drop the old files column
                $table->dropColumn('files');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patches', function (Blueprint $table) {
            if (Schema::hasColumn('patches', 'file')) {
                $table->dropColumn('file');
            }

            if (! Schema::hasColumn('patches', 'files')) {
                $table->json('files')->nullable()->after('patch_name');
            }
        });
    }
};
