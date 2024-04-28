<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create three different users with different number of tasks.
        // We need fixed password for generating tokens.
        User::factory()
            ->hasTasks(10)
            ->create([
                'name' => 'user1',
                'email' => 'user1@example.com',
                'password' => 'user1pw'
            ]);

        User::factory()
            ->create([
                'name' => 'user2',
                'email' => 'user2@example.com',
                'password' => 'user2pw'
            ]);

        User::factory()
            ->hasTasks(2000)
            ->create([
                'name' => 'user3',
                'email' => 'user3@example.com',
                'password' => 'user3pw'
            ]);

        // Create an admin user
        User::factory()->admin()->create(
            [
                'name' => 'adminuser',
                'email' => 'admin@example.com',
                'password' => 'adm3n',
            ]
        );

        // Seed projects.
        Project::factory(20)->create();

        // Generate tasks that are associated with a project.
        Task::factory(20)
            ->withProjects()
            ->withUsers()
            ->create();

        // Create projects.
        // Project::factory(20)
        // ->hasTasks(10)
        // ->create();
    }
}
