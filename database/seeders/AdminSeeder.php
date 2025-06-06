<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User (only if doesn't exist)
        User::firstOrCreate(
            ['email' => 'admin@spup.edu.ph'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'administrator',
                'student_id' => null
            ]
        );
    }
}
