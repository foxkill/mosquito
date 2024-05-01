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

        $user3 = User::factory()
            ->hasTasks(500)
            ->create([
                'name' => 'user3',
                'email' => 'user3@example.com',
                'password' => 'user3pw'
            ]);
        
        Task::factory(100)
            ->notOverdue()
            ->for($user3)
            ->create();

        // We want to have 1400 overdue tasks for perf testing.
        Task::factory(1400)->overdue()->create(
            ['user_id' => $user3->id]
        );

        // Create an admin user
        User::factory()->admin()->create([
            'name' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => 'adm3n',
        ]);

        // Seed projects.
        Project::factory(20)->create();

        // Generate tasks that are associated with a project.
        Task::factory(20)
            ->withProjects()
            ->withUsers()
            ->create();
    }
}
