<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Insert default system settings for production use.
     * This replaces the SystemSettingSeeder for production environments.
     */
    public function up(): void
    {
        // Insert default system settings only if they don't exist
        $defaultSettings = [
            [
                'key' => 'application_status',
                'value' => 'open',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'current_semester',
                'value' => '1st Semester',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'current_academic_year',
                'value' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($defaultSettings as $setting) {
            // Only insert if the setting doesn't already exist
            $exists = DB::table('system_settings')
                ->where('key', $setting['key'])
                ->exists();

            if (!$exists) {
                DB::table('system_settings')->insert($setting);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * Remove the default system settings.
     */
    public function down(): void
    {
        // Remove the default settings we inserted
        DB::table('system_settings')->whereIn('key', [
            'application_status',
            'current_semester',
            'current_academic_year'
        ])->delete();
    }
};
