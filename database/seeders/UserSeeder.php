<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Student User
        User::create([
            'name' => 'Student User',
            'email' => 'student@spup.edu.ph',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'student_id' => '2024-123456'
        ]);

        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@spup.edu.ph',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Create additional test students
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@spup.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => '2024-111111'
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane.smith@spup.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => '2024-222222'
        ]);

        User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike.johnson@spup.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'student_id' => '2024-333333'
        ]);
    }
}
