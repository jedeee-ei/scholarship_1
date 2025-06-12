<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call individual seeders
        $this->call([
            AdminSeeder::class,
            DepartmentSeeder::class,
            CourseSeeder::class,
            SubjectSeeder::class,
        ]);
    }
}
