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
        Schema::table('grantees', function (Blueprint $table) {
            // Remove the grantee_id column
            $table->dropColumn('grantee_id');

            // Make student_id unique if it's not already
            $table->unique('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grantees', function (Blueprint $table) {
            // Add back the grantee_id column
            $table->string('grantee_id')->unique()->after('id');

            // Remove unique constraint from student_id
            $table->dropUnique(['student_id']);
        });
    }
};
