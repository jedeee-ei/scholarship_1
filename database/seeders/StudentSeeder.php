<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Student User (only if doesn't exist)
        User::firstOrCreate(
            ['email' => 'student@spup.edu.ph'],
            [
                'name' => 'Student User',
                'password' => Hash::make('student123'),
                'role' => 'student',
                'student_id' => '2024-123456'
            ]
        );
    }
}
