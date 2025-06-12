<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SystemSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add default application status setting if it doesn't exist
        SystemSetting::firstOrCreate(
            ['key' => 'application_status'],
            ['value' => 'closed']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the application status setting
        SystemSetting::where('key', 'application_status')->delete();
    }
};
