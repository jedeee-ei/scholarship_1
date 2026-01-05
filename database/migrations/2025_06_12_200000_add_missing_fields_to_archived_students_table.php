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
        Schema::table('archived_students', function (Blueprint $table) {
            // Add missing fields for better data preservation
            $table->string('middle_name')->nullable()->after('last_name');
            $table->string('government_benefactor_type')->nullable()->after('scholarship_type');
            $table->string('employee_name')->nullable()->after('government_benefactor_type');
            $table->string('employee_relationship')->nullable()->after('employee_name');
            $table->string('scholarship_name')->nullable()->after('employee_relationship');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archived_students', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'government_benefactor_type',
                'employee_name',
                'employee_relationship',
                'scholarship_name'
            ]);
        });
    }
};
