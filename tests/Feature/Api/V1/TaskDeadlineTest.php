<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\TaskTokenEnum;
use Laravel\Sanctum\Sanctum;
use App\Enums\StateEnum;
use App\Models\User;
use App\Models\Task;
use Tests\TestCase;

class TaskDeadlineTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
    use WithFaker;

    /**
     * It should be able to update the deadline.
     */
    public function test_can_update_the_deadline_of_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();

        $task = Task::factory()->create(
            $expectedData = [
                'title' => $this->faker->sentence(),
                'state' => StateEnum::InProgess->value,
                'description' => $this->faker->sentence(),
                'user_id' => $user->id,
            ],
        );

        // Create additional task so we can verify the correct one was updated.
        $taskOther = Task::factory()->create(['user_id' => $user->id]);

        $deadline = now()->addDay();

        // Act
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);
        $response = $this->patchJson(
            route('tasks.deadline', ['task' => $task]),
            ['deadline' => $deadline]
        );

        // Assert
        $response
            ->assertOk();

        // Assert that the data was actually written.
        $this->assertDatabaseHas(
            'tasks',
            array_merge(
                $expectedData, 
                [
                    'id' => $task->id,
                    'deadline' => $deadline,
                ]
            )
        );
    }

    /**
     * It should be able to update the deadline with normal put request.
     */
    public function test_can_not_update_the_deadline_if_method_is_not_patch(): void
    {
        // Arrange.
        $user = User::factory()->create();

        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        // Act
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);
        $response = $this->putJson(
            route('tasks.deadline', ['task' => $task]),
            ['deadline' => now()->addDay()]
        );

        // Assert
        $response->assertMethodNotAllowed();
    }

    /**
     * It should be able to reset the deadline
     */
    public function test_can_not_reset_the_deadline(): void
    {
        // Arrange.
        $user = User::factory()->create();

        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        // Act
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);

        $response = $this->patchJson(
            route('tasks.deadline', ['task' => $task]),
            ['deadline' => null]
        );

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
