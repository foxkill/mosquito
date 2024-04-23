<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\StateEnum;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence,
            'description' => fake()->words(asText:true),
            'state' => fake()->randomElement(array_map(fn($case) => $case->value, StateEnum::cases())),
            'user_id' => User::factory(),
        ];
    }
}
