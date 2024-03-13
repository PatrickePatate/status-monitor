<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default user
        \App\Models\User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@status-monitor.fr',
            'password' => Hash::make('@dm!n')
        ]);

        // Create fake data
        $this->call(ServicesSeeder::class);
        $this->call(MetricsSeeder::class);
        
    }
}
