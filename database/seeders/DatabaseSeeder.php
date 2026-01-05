<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This seeder is designed for production use and only includes
     * essential data required for system operation.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding production-ready data...');

        // Essential seeders for production
        $this->call([
            AdminSeeder::class,  // Creates default admin account
        ]);

        $this->command->info('✅ Production seeding completed!');
        $this->command->line('');
        $this->command->info('📋 What was seeded:');
        $this->command->line('   • Default admin account (admin@spup.edu.ph)');
        $this->command->line('');
        $this->command->info('📝 Note: System settings are handled by migrations');
        $this->command->line('   • Current semester/year: Set via migration');
        $this->command->line('   • Application status: Set via migration');
    }
}
