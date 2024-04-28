<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\TaskTokenEnum;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Task;
use Tests\TestCase;

class TaskOverdueTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
    use WithFaker;

    /**
     * It should return all overdue messages for an user.
     */
    public function test_should_list_overdue_tasks_for_an_user(): void
    {
        // Arrange.
        $user = User::factory()->create();

        $tasks = Task::factory(3)->create(
            [
                'user_id' => $user->id,
            ]
        )->each(function ($task, $key) {
            $task->update(['deadline' => now()->subDays($key + 1)]);
        });

        $taskNotOverdue = Task::factory(2)->create(
            [
                'user_id' => $user->id,
                'deadline' => now()->addDays(4),
            ]
        );

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Read->value]);
        $response = $this->getJson(
            route('tasks.overdue')
        );

        // Assert - HTTP status, structure, and most overdue task first.
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'title',
                            'description',
                            'state',
                            'project_id',
                            'deadline',
                        ]
                    ]
                ]
            )
            ->assertJson(fn(AssertableJson $json) => 
                $json->has('data', count($tasks))
                     ->where('data.0.deadline', $tasks[0]->deadline->toJson())
            );
    }

    /**
     * It should return all overdue messages.
     */
    public function test_should_list_overdue_tasks(): void
    {
        // Arrange.
        // Act.
        // Assert.
        $this->assertTrue(false);
    }
}
