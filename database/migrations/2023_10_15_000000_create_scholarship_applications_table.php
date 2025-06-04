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
        Schema::create('scholarship_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_id')->unique();
            $table->string('scholarship_type');
            $table->string('student_id');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('sex')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('education_stage')->nullable();
            $table->string('department')->nullable();
            $table->string('course')->nullable();
            $table->string('year_level')->nullable();
            $table->string('grade_level')->nullable();
            $table->string('strand')->nullable();
            $table->string('gwa')->nullable();
            $table->string('semester')->nullable();
            $table->string('academic_year')->nullable();
            $table->string('father_last_name')->nullable();
            $table->string('father_first_name')->nullable();
            $table->string('father_middle_name')->nullable();
            $table->string('mother_last_name')->nullable();
            $table->string('mother_first_name')->nullable();
            $table->string('mother_middle_name')->nullable();
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('disability')->nullable();
            $table->string('indigenous')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email');
            // Employee scholarship fields
            $table->string('employee_name')->nullable();
            $table->string('employee_relationship')->nullable();
            $table->string('employee_department')->nullable();
            $table->string('employee_position')->nullable();
            // Private scholarship fields
            $table->string('scholarship_name')->nullable();
            $table->string('other_scholarship')->nullable();
            $table->string('status')->default('Pending Review');
            $table->boolean('is_active')->default(false);
            $table->date('active_until')->nullable();
            $table->boolean('is_renewal')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_applications');
    }
};

