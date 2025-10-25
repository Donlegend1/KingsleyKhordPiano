<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 random users
        User::factory(10)->create();

        // Create a test user
        User::factory()->create([
            'first_name' => 'Test User',
            'last_name' => 'test@example.com',
            'role' => 'member',
            'password' => Hash::make('password'), 
        ]);

        // Create an admin user
        User::factory()->create([
            'first_name' => ' User',
            'last_name' => 'Admin',
            'email' => 'admin@kingsleykhord.com',
            'password' => Hash::make('password'),
            'role' => 'admin', 
        ]);

        $this->call(PlanSeeder::class);
        $this->call([
            CourseCategorySeeder::class
        ]);
    }
}
