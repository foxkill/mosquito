<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    use WithFaker;

    public function test_no_unauthorized_access() 
    {
        // Arrange.
        $user = User::factory()->create();
        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        // Act - we do not impersonate the user.
        $response = $this->getJson(route('tasks.index'));

        // Assert - repsonse code, data count, id & title match.
        $response->assertStatus(401);
    }

    /**
     * The api should return a list of Tasks (exam 1).
     */
    public function test_should_list_tasks(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        // Act.
        $response = $this->actingAs($user)->getJson(route('tasks.index'));

        // Assert - repsonse code, data count, id & title match.
        $response
            ->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                Arr::only($task->toArray(), ['title', 'state']) 
            ]);
    }

    /**
     * It should create a task on behalf of the user.
     */
    public function test_should_create_a_task(): void 
    {
        // Arrange.
        $user = User::factory()->create();

        // Act.
        $response = $this->actingAs($user)->postJson(
            route('tasks.store'),
            [
                'user_id' => $user->id,
                'title' => $this->faker->sentence(),
                'state' => 'todo',
                'description' => $this->faker->realText(),
            ]
        );

        // Assert.
        $response
            ->assertStatus(201);
    }
}
