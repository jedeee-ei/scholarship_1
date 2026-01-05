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
            $table->json('documents')->nullable()->after('other_scholarship');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grantees', function (Blueprint $table) {
            $table->dropColumn('documents');
        });
    }
};
