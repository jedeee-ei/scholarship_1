<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update education_stage from 'BSU' to 'BEU' in scholarship_applications table
        DB::table('scholarship_applications')
            ->where('education_stage', 'BSU')
            ->update(['education_stage' => 'BEU']);

        // Update education_stage from 'BSU' to 'BEU' in grantees table (if it exists)
        if (Schema::hasTable('grantees')) {
            DB::table('grantees')
                ->where('education_stage', 'BSU')
                ->update(['education_stage' => 'BEU']);
        }

        // Update education_stage from 'BSU' to 'BEU' in archived_students table (if it exists and has the column)
        if (Schema::hasTable('archived_students') && Schema::hasColumn('archived_students', 'education_stage')) {
            DB::table('archived_students')
                ->where('education_stage', 'BSU')
                ->update(['education_stage' => 'BEU']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert education_stage from 'BEU' back to 'BSU' in scholarship_applications table
        DB::table('scholarship_applications')
            ->where('education_stage', 'BEU')
            ->update(['education_stage' => 'BSU']);

        // Revert education_stage from 'BEU' back to 'BSU' in grantees table (if it exists)
        if (Schema::hasTable('grantees')) {
            DB::table('grantees')
                ->where('education_stage', 'BEU')
                ->update(['education_stage' => 'BSU']);
        }

        // Revert education_stage from 'BEU' back to 'BSU' in archived_students table (if it exists and has the column)
        if (Schema::hasTable('archived_students') && Schema::hasColumn('archived_students', 'education_stage')) {
            DB::table('archived_students')
                ->where('education_stage', 'BEU')
                ->update(['education_stage' => 'BSU']);
        }
    }
};
