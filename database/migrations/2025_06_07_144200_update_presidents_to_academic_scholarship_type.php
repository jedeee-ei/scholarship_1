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
        // Update scholarship_type from 'presidents' to 'academic' in scholarship_applications table
        DB::table('scholarship_applications')
            ->where('scholarship_type', 'presidents')
            ->update(['scholarship_type' => 'academic']);

        // Update scholarship_type from 'presidents' to 'academic' in grantees table (if it exists)
        if (Schema::hasTable('grantees')) {
            DB::table('grantees')
                ->where('scholarship_type', 'presidents')
                ->update(['scholarship_type' => 'academic']);
        }

        // Update scholarship_type from 'presidents' to 'academic' in archived_students table (if it exists and has the column)
        if (Schema::hasTable('archived_students') && Schema::hasColumn('archived_students', 'scholarship_type')) {
            DB::table('archived_students')
                ->where('scholarship_type', 'presidents')
                ->update(['scholarship_type' => 'academic']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert scholarship_type from 'academic' back to 'presidents' in scholarship_applications table
        DB::table('scholarship_applications')
            ->where('scholarship_type', 'academic')
            ->update(['scholarship_type' => 'presidents']);

        // Revert scholarship_type from 'academic' back to 'presidents' in grantees table (if it exists)
        if (Schema::hasTable('grantees')) {
            DB::table('grantees')
                ->where('scholarship_type', 'academic')
                ->update(['scholarship_type' => 'presidents']);
        }

        // Revert scholarship_type from 'academic' back to 'presidents' in archived_students table (if it exists and has the column)
        if (Schema::hasTable('archived_students') && Schema::hasColumn('archived_students', 'scholarship_type')) {
            DB::table('archived_students')
                ->where('scholarship_type', 'academic')
                ->update(['scholarship_type' => 'presidents']);
        }
    }
};
