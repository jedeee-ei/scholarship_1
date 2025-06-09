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
        // Only add columns that are missing from the users table
        $columnsToAdd = [];

        if (!Schema::hasColumn('users', 'department')) {
            $columnsToAdd[] = 'department';
        }
        if (!Schema::hasColumn('users', 'course')) {
            $columnsToAdd[] = 'course';
        }
        if (!Schema::hasColumn('users', 'year_level')) {
            $columnsToAdd[] = 'year_level';
        }
        if (!Schema::hasColumn('users', 'status')) {
            $columnsToAdd[] = 'status';
        }
        if (!Schema::hasColumn('users', 'password_changed')) {
            $columnsToAdd[] = 'password_changed';
        }

        if (!empty($columnsToAdd)) {
            Schema::table('users', function (Blueprint $table) use ($columnsToAdd) {
                if (in_array('department', $columnsToAdd)) {
                    $table->string('department')->nullable();
                }
                if (in_array('course', $columnsToAdd)) {
                    $table->string('course')->nullable();
                }
                if (in_array('year_level', $columnsToAdd)) {
                    $table->string('year_level')->nullable();
                }
                if (in_array('status', $columnsToAdd)) {
                    $table->string('status')->default('active');
                }
                if (in_array('password_changed', $columnsToAdd)) {
                    $table->boolean('password_changed')->default(false);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only drop columns that were added by this migration
            $columnsToDrop = [];

            if (Schema::hasColumn('users', 'department')) {
                $columnsToDrop[] = 'department';
            }
            if (Schema::hasColumn('users', 'course')) {
                $columnsToDrop[] = 'course';
            }
            if (Schema::hasColumn('users', 'year_level')) {
                $columnsToDrop[] = 'year_level';
            }
            if (Schema::hasColumn('users', 'status')) {
                $columnsToDrop[] = 'status';
            }
            if (Schema::hasColumn('users', 'password_changed')) {
                $columnsToDrop[] = 'password_changed';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
