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
        Schema::table('scholarship_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('scholarship_applications', 'is_renewal')) {
                $table->boolean('is_renewal')->default(false)->after('status');
            }
            if (!Schema::hasColumn('scholarship_applications', 'previous_archive_id')) {
                $table->unsignedBigInteger('previous_archive_id')->nullable()->after('is_renewal');
                // Add foreign key constraint to archived_students table
                $table->foreign('previous_archive_id')->references('id')->on('archived_students')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarship_applications', function (Blueprint $table) {
            if (Schema::hasColumn('scholarship_applications', 'previous_archive_id')) {
                $table->dropForeign(['previous_archive_id']);
                $table->dropColumn('previous_archive_id');
            }
            if (Schema::hasColumn('scholarship_applications', 'is_renewal')) {
                $table->dropColumn('is_renewal');
            }
        });
    }
};
