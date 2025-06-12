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
        // Update scholarship_applications table
        DB::table('scholarship_applications')
            ->where('scholarship_type', 'private')
            ->update(['scholarship_type' => 'alumni']);

        // Update grantees table
        DB::table('grantees')
            ->where('scholarship_type', 'private')
            ->update(['scholarship_type' => 'alumni']);

        // Update archived_students table
        DB::table('archived_students')
            ->where('scholarship_type', 'private')
            ->update(['scholarship_type' => 'alumni']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert scholarship_applications table
        DB::table('scholarship_applications')
            ->where('scholarship_type', 'alumni')
            ->update(['scholarship_type' => 'private']);

        // Revert grantees table
        DB::table('grantees')
            ->where('scholarship_type', 'alumni')
            ->update(['scholarship_type' => 'private']);

        // Revert archived_students table
        DB::table('archived_students')
            ->where('scholarship_type', 'alumni')
            ->update(['scholarship_type' => 'private']);
    }
};
