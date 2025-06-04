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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('code', 20); // PED101, ITE 101, etc.
            $table->string('title'); // Subject title
            $table->integer('units'); // Credit units
            $table->integer('year_level'); // 1, 2, 3, 4, 5
            $table->enum('semester', ['1st Semester', '2nd Semester', 'Summer']); // Semester
            $table->text('description')->nullable();
            $table->text('prerequisites')->nullable(); // JSON array of prerequisite subject codes
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Composite index for efficient queries
            $table->index(['course_id', 'year_level', 'semester']);
            // Unique constraint to prevent duplicate subjects
            $table->unique(['course_id', 'code', 'year_level', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
