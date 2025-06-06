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
        Schema::create('grantees', function (Blueprint $table) {
            $table->id();
            $table->string('grantee_id')->unique();
            $table->string('application_id'); // Reference to original application
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

            // CHED specific fields
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

            // Contact information
            $table->string('contact_number')->nullable();
            $table->string('email');
            $table->text('address')->nullable();

            // Employee scholarship fields
            $table->string('employee_name')->nullable();
            $table->string('employee_relationship')->nullable();
            $table->string('employee_department')->nullable();
            $table->string('employee_position')->nullable();

            // Private scholarship fields
            $table->string('scholarship_name')->nullable();
            $table->text('other_scholarship')->nullable();

            // Grantee specific fields
            $table->date('approved_date');
            $table->string('approved_by'); // Admin who approved
            $table->string('status')->default('Active'); // Active, Inactive, Graduated, Terminated
            $table->date('scholarship_start_date');
            $table->date('scholarship_end_date')->nullable();
            $table->decimal('scholarship_amount', 10, 2)->nullable();
            $table->text('special_conditions')->nullable(); // Any special conditions or requirements
            $table->text('notes')->nullable(); // Admin notes

            // Renewal tracking
            $table->boolean('is_renewable')->default(false);
            $table->integer('renewal_count')->default(0);
            $table->date('next_renewal_date')->nullable();

            // Performance tracking
            $table->string('current_gwa')->nullable();
            $table->text('performance_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['scholarship_type', 'status']);
            $table->index(['student_id', 'scholarship_type']);
            $table->index('approved_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grantees');
    }
};
