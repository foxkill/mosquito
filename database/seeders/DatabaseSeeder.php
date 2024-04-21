<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create three diffe rent users with different number of tasks.
        User::factory()
            ->hasTasks(10)
            ->create([
                'name' => 'user1',
                'email' => 'user1@example.com',
            ]);

        User::factory()
            ->create([
                'name' => 'user2',
                'email' => 'user2@example.com',
            ]);

        User::factory()
            ->hasTasks(2000)
            ->create([
                'name' => 'user3',
                'email' => 'user3@example.com',
            ]);
    }
}
