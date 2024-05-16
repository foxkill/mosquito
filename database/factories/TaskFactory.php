<?php

namespace Database\Factories;

use App\Enums\StateEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence,
            'description' => fake()->words(asText: true),
            'state' => fake()->randomElement(array_map(fn ($case) => $case->value, StateEnum::cases())),
            // We have always a user.
            'user_id' => User::factory(),
        ];
    }

    /**
     * Randomly associate tasks with projects.
     */
    public function withProjects()
    {
        return $this->afterCreating(function (Task $task) {
            $project = (rand(0, 1)) ? Project::inRandomOrder()->first() : null;
            $task->project()->associate($project)->save();
        });
    }

    /**
     * Use only existing users to associate to tasks.
     */
    public function withUsers()
    {
        return $this->afterCreating(function (Task $task) {
            $task->user()->associate(User::inRandomOrder()->first())->save();
        });
    }

    /**
     * Mark a project as overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => now()->subDays(rand(1, 10)),
        ]);
    }

    /**
     * Mark a project as not overdue.
     */
    public function notOverdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => now()->addDays(rand(1, 10)),
        ]);
    }

    /**
     * Mark a project as todo.
     */
    public function todo(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => StateEnum::Todo->value,
        ]);
    }

    /**
     * Mark a project as in progress.
     */
    public function inProgess(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => StateEnum::InProgess->value,
        ]);
    }

    /**
     * Mark a project as done.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => StateEnum::Done->value,
        ]);
    }
}
