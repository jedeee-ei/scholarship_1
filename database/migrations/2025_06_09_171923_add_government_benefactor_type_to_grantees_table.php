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
            $table->string('government_benefactor_type')->nullable()->after('scholarship_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grantees', function (Blueprint $table) {
            $table->dropColumn('government_benefactor_type');
        });
    }
};
