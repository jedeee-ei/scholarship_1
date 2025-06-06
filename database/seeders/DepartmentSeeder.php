<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'code' => 'SITE',
                'name' => 'School of Information Technology and Engineering',
                'description' => 'Department offering IT and Engineering programs',
                'is_active' => true
            ],
            [
                'code' => 'SASTE',
                'name' => 'School of Arts, Sciences, Teacher Education',
                'description' => 'Department offering Arts, Sciences, and Education programs',
                'is_active' => true
            ],
            [
                'code' => 'SBAHM',
                'name' => 'School of Business Administration and Hospitality Management',
                'description' => 'Department offering Business and Hospitality programs',
                'is_active' => true
            ],
            [
                'code' => 'SNAHS',
                'name' => 'School of Nursing and Allied Health Sciences',
                'description' => 'Department offering Nursing and Health Sciences programs',
                'is_active' => true
            ]
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['code' => $department['code']],
                $department
            );
        }
    }
}
