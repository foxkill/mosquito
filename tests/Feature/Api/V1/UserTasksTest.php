<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\UserTokenEnum;
use Laravel\Sanctum\Sanctum;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class UserTasksTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
    use WithFaker;

    /**
     * It should retrieve tasks for a specific user.
     */
    public function test_should_retrieve_tasks_of_another_user_if_admin(): void
    {
        // Arrange.
        $user = User::factory()->admin()->create();
        Task::factory(2)->for($user)->create();

        $otherUser = User::factory()->create();
        $tasksOtherUser = Task::factory(4)
            ->notOverdue()
            ->for($otherUser)
            ->create();

        // Act.
        Sanctum::actingAs($user, [UserTokenEnum::ReadUserTasks->value]);

        $response = $this
            ->getJson(
                route('user.tasks', ['user' => $otherUser])
            );

        // Assert.
        $response
            ->assertOk()
            ->assertJsonCount(count($tasksOtherUser), 'data.tasks');
    }

    /**
     * It should not retrieve tasks for another user when the user
     * is not an admin user.
     */
    public function test_should_not_retrieve_tasks_of_another_user(): void
    {
        // Arrange.
        $user = User::factory()->create();
        Task::factory(2)->for($user)->create();

        $otherUser = User::factory()->create();
        Task::factory(4)
            ->for($otherUser)
            ->create();

        // Act.
        Sanctum::actingAs($user, [UserTokenEnum::ReadUserTasks->value]);

        $response = $this
            ->getJson(
                route('user.tasks', ['user' => $otherUser])
            );

        // Assert.
        $response
            ->assertOk()
            ->assertJsonCount(0, 'data.tasks');
    }
}
