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
            // Make application_id nullable since manual entries don't have application_id
            $table->string('application_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grantees', function (Blueprint $table) {
            // Revert application_id to not nullable
            $table->string('application_id')->nullable(false)->change();
        });
    }
};
