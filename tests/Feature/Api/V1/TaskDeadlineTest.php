<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\TaskTokenEnum;
use Laravel\Sanctum\Sanctum;
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
     * A basic feature test example.
     */
    public function test_can_update_the_deadline_of_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();

        $tasks = Task::factory(2)->create(
            ['user_id' => $user->id]
        );
        
        // Act
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);
        $response = $this->patchJson(
            route('tasks.deadline', ['task' => $tasks->first()])
        );

        // Assert
        $response->assertStatus(Response::HTTP_OK);
    }
}
