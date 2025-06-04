<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ScholarshipApplication;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@spup.edu.ph',
            'password' => bcrypt('admin123'),
            'role' => 'administrator',
        ]);

        // Create student user
        $student = User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@spup.edu.ph',
            'password' => bcrypt('student123'),
            'role' => 'student',
            'student_id' => '2023-12345',
        ]);

        // Create additional students
        $students = User::factory()->count(20)->create();

        // Create scholarship applications for the demo student
        ScholarshipApplication::factory()->create([
            'student_id' => $student->student_id,
            'first_name' => 'Student',
            'last_name' => 'User',
            'status' => 'Approved',
            'scholarship_type' => 'ched',
        ]);

        ScholarshipApplication::factory()->create([
            'student_id' => $student->student_id,
            'first_name' => 'Student',
            'last_name' => 'User',
            'status' => 'Rejected',
            'scholarship_type' => 'presidents',
        ]);

        // Create random scholarship applications
        ScholarshipApplication::factory()->count(10)->pending()->create();
        ScholarshipApplication::factory()->count(5)->approved()->create();
        ScholarshipApplication::factory()->count(3)->rejected()->create();

        // Create applications for specific students
        foreach ($students->take(10) as $student) {
            ScholarshipApplication::factory()->create([
                'student_id' => $student->student_id,
                'first_name' => $student->name,
                'last_name' => fake()->lastName(),
            ]);
        }
    }
}

