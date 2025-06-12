<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get departments - ensure they exist
        $site = Department::where('code', 'SITE')->first();
        $saste = Department::where('code', 'SASTE')->first();
        $sbahm = Department::where('code', 'SBAHM')->first();
        $snahs = Department::where('code', 'SNAHS')->first();

        // Check if all required departments exist
        if (!$site || !$saste || !$sbahm || !$snahs) {
            $this->command->error('One or more required departments not found. Please run DepartmentSeeder first.');
            return;
        }

        $courses = [
            // SITE Courses
            [
                'department_id' => $site->id,
                'code' => 'BSIT',
                'name' => 'Bachelor of Science in Information Technology',
                'description' => 'Information Technology program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $site->id,
                'code' => 'BSCS',
                'name' => 'Bachelor of Science in Computer Science',
                'description' => 'Computer Science program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $site->id,
                'code' => 'BSCE',
                'name' => 'Bachelor of Science in Civil Engineering',
                'description' => 'Civil Engineering program',
                'duration_years' => 5,
                'is_active' => true
            ],
            [
                'department_id' => $site->id,
                'code' => 'BSEE',
                'name' => 'Bachelor of Science in Environmental and Sanitary Engineering',
                'description' => 'Environmental and Sanitary Engineering program',
                'duration_years' => 5,
                'is_active' => true
            ],
            [
                'department_id' => $site->id,
                'code' => 'BSCpE',
                'name' => 'Bachelor of Science in Computer Engineering',
                'description' => 'Computer Engineering program',
                'duration_years' => 5,
                'is_active' => true
            ],
            [
                'department_id' => $site->id,
                'code' => 'BLIS',
                'name' => 'Bachelor of Library and Information Science',
                'description' => 'Library and Information Science program',
                'duration_years' => 4,
                'is_active' => true
            ],

            // SASTE Courses
            [
                'department_id' => $saste->id,
                'code' => 'BEED',
                'name' => 'Bachelor of Elementary Education',
                'description' => 'Elementary Education program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSED',
                'name' => 'Bachelor of Secondary Education',
                'description' => 'Secondary Education program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSPsych',
                'name' => 'Bachelor of Science in Psychology',
                'description' => 'Psychology program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BAELS',
                'name' => 'Bachelor of Arts in English Language Studies',
                'description' => 'English Language Studies program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSBio',
                'name' => 'Bachelor of Science in Biology',
                'description' => 'Biology program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSBioMicro',
                'name' => 'Bachelor of Science in Biology Major in Microbiology',
                'description' => 'Biology Major in Microbiology program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSSW',
                'name' => 'Bachelor of Science in Social Work',
                'description' => 'Social Work program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BSPA',
                'name' => 'Bachelor of Science in Public Administration',
                'description' => 'Public Administration program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $saste->id,
                'code' => 'BPED',
                'name' => 'Bachelor of Physical Education',
                'description' => 'Physical Education program',
                'duration_years' => 4,
                'is_active' => true
            ],

            // SBAHM Courses
            [
                'department_id' => $sbahm->id,
                'code' => 'BSBA',
                'name' => 'Bachelor of Science in Business Administration',
                'description' => 'Business Administration program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $sbahm->id,
                'code' => 'BSHM',
                'name' => 'Bachelor of Science in Hospitality Management',
                'description' => 'Hospitality Management program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $sbahm->id,
                'code' => 'BSTM',
                'name' => 'Bachelor of Science in Tourism Management',
                'description' => 'Tourism Management program',
                'duration_years' => 4,
                'is_active' => true
            ],

            // SNAHS Courses
            [
                'department_id' => $snahs->id,
                'code' => 'BSN',
                'name' => 'Bachelor of Science in Nursing',
                'description' => 'Nursing program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $snahs->id,
                'code' => 'BSMT',
                'name' => 'Bachelor of Science in Medical Technology',
                'description' => 'Medical Technology program',
                'duration_years' => 4,
                'is_active' => true
            ],
            [
                'department_id' => $snahs->id,
                'code' => 'BSPT',
                'name' => 'Bachelor of Science in Physical Therapy',
                'description' => 'Physical Therapy program',
                'duration_years' => 4,
                'is_active' => true
            ]
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(
                ['code' => $course['code']],
                $course
            );
        }
    }
}
