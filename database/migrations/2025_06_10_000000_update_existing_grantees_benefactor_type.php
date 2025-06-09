<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing grantees with government_benefactor_type from their original applications
        DB::statement("
            UPDATE grantees g
            INNER JOIN scholarship_applications sa ON g.application_id = sa.application_id
            SET g.government_benefactor_type = sa.government_benefactor_type
            WHERE g.scholarship_type = 'government' 
            AND sa.government_benefactor_type IS NOT NULL
            AND g.government_benefactor_type IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set government_benefactor_type to null for government grantees
        DB::table('grantees')
            ->where('scholarship_type', 'government')
            ->update(['government_benefactor_type' => null]);
    }
};
