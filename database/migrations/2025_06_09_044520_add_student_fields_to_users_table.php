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
        Schema::table('users', function (Blueprint $table) {
            // Add only the missing columns (first_name, last_name, student_id, role already exist)
            $table->string('department')->nullable()->after('student_id');
            $table->string('course')->nullable()->after('department');
            $table->string('year_level')->nullable()->after('course');
            $table->string('status')->default('active')->after('year_level');
            $table->boolean('password_changed')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove only the columns we added in this migration
            $table->dropColumn([
                'department',
                'course',
                'year_level',
                'status',
                'password_changed'
            ]);
        });
    }
};
