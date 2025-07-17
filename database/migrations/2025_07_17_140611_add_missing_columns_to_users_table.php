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
            // Add middle_name column if it doesn't exist
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('last_name');
            }

            // Add contact_number column if it doesn't exist
            if (!Schema::hasColumn('users', 'contact_number')) {
                $table->string('contact_number', 11)->nullable()->after('password_changed');
            }

            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('contact_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the columns if they exist
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('users', 'contact_number')) {
                $table->dropColumn('contact_number');
            }

            if (Schema::hasColumn('users', 'middle_name')) {
                $table->dropColumn('middle_name');
            }
        });
    }
};
