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
        Schema::create('archived_students', function (Blueprint $table) {
            $table->id();
            $table->string('original_application_id');
            $table->string('student_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('contact_number')->nullable();
            $table->string('course');
            $table->string('department')->nullable();
            $table->string('year_level')->nullable();
            $table->decimal('gwa', 3, 2)->nullable();
            $table->string('scholarship_type');
            $table->string('archived_semester');
            $table->string('archived_academic_year');
            $table->enum('archive_type', ['masterlist', 'inactive'])->default('masterlist'); // Type of archive
            $table->text('remarks')->nullable(); // Reason for archiving (e.g., Transferred, Graduated, etc.)
            $table->timestamp('archived_at');
            $table->string('archived_by')->nullable(); // Admin who archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_students');
    }
};
