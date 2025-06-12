<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set application status to open
        SystemSetting::set('application_status', 'open');
        
        // Set current semester and academic year if not already set
        if (!SystemSetting::get('current_semester')) {
            SystemSetting::set('current_semester', '1st Semester');
        }
        
        if (!SystemSetting::get('current_academic_year')) {
            SystemSetting::set('current_academic_year', '2024-2025');
        }
    }
}
