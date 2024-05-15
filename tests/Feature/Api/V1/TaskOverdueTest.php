<?php

namespace Tests\Feature\Api\V1;

use App\Enums\Auth\Token\TaskTokenEnum;
use App\Enums\StateEnum;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
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

        $tasksOverdue = Task::factory(3)
            ->overdue()
            ->create(
                [
                    'user_id' => $user->id,
                    'state' => StateEnum::InProgess,
                ]
            );

        $tasksNotOverdue = Task::factory(2)
            ->notOverdue()
            ->create(
                [
                    'user_id' => $user->id,
                    'state' => StateEnum::InProgess,
                ]
            );

        $mostOverdue = $tasksOverdue->min('deadline');

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Read->value]);
        $response = $this->getJson(route('tasks.overdue'));

        // Assert - HTTP status, structure, and most overdue task first.
        $response->assertOk()
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'title',
                            'description',
                            'state',
                            'project_id',
                            'deadline',
                        ],
                    ],
                ]
            )
            ->assertJson(
                fn (AssertableJson $json) => $json->has('data', count($tasksOverdue))
                    ->where('data.0.deadline', $mostOverdue->toJson())
            );
    }

    /**
     * It should return all *overdue* tasks for an admin and tasks
     * from other users where the deadline has expired.
     */
    public function test_should_list_overdue_tasks_for_an_admin(): void
    {
        // Arrange.
        $adminUser = User::factory()->admin()->create();
        $otherUser = User::factory()->create();
        $user = User::factory()->create();

        // The admin user should not have task.
        $taskAdminUserOverdue = Task::factory(1)
            ->overdue()
            ->create(
                ['user_id' => $adminUser->id]
            );

        $taskAdminUserNotOverdue = Task::factory(3)
            ->notOverdue()
            ->create(
                ['user_id' => $adminUser->id]
            );

        // User
        $tasksUserOverdue = Task::factory(3)
            ->overdue()
            ->create(
                ['user_id' => $user->id]
            );

        $tasksUserNotOverdue = Task::factory(2)
            ->notOverdue()
            ->create(['user_id' => $user->id]);

        // OtherUser
        $tasksOtherUser = Task::factory(5)
            ->create(
                ['user_id' => $otherUser->id]
            );

        Task::factory(2)
            ->notOverdue()
            ->create(['user_id' => $otherUser->id]);

        $taskOtherUserOverdue = Task::factory(1)
            ->overdue()
            ->create(['user_id' => $otherUser->id]);

        $countTasksOverdueAndOwnTasks =
            count($taskAdminUserOverdue) +
            count($tasksUserOverdue) +
            count($taskOtherUserOverdue);

        // Act.
        Sanctum::actingAs($adminUser, [TaskTokenEnum::Read->value]);
        $response = $this->getJson(route('tasks.overdue'));

        // Assert.
        $response->assertOk()
            ->assertJsonCount(
                $countTasksOverdueAndOwnTasks,
                'data'
            );
    }

    /**
     * Admin can update overdue tasks of a user.
     */
    public function test_admin_can_update_overdue_tasks_for_users(): void
    {
        // Arrange.
        $adminUser = User::factory()->admin()->create();
        $otherUser = User::factory()->create();
        $user = User::factory()->create();

        // Create overdue tasks.
        $tasksUserOverdue = Task::factory(3)
            ->overdue()
            ->create(
                [
                    'user_id' => $user->id,
                    'state' => StateEnum::InProgess->value,
                ]
            );

        // Create tasks that are not overdue, but have a deadline.
        $tasksOtherUserNotOverdue = Task::factory(5)
            ->notOverdue()
            ->create(
                ['user_id' => $otherUser->id]
            );

        // Act.
        Sanctum::actingAs($adminUser, [TaskTokenEnum::Update->value]);
        $response = $this->putJson(
            route('tasks.update', $tasksUserOverdue[1]),
            ['state' => StateEnum::Done]
        );

        // Assert.
        $response->assertOk();

        $this->assertDatabaseHas(
            'tasks',
            [
                'id' => $tasksUserOverdue[1]->id,
                'state' => StateEnum::Done->value,
            ]
        );
    }

    /**
     * Admin can not update tasks that are not overdue.
     */
    public function test_admin_can_not_update_not_overdue_tasks_for_users(): void
    {
        // Arrange.
        $adminUser = User::factory()->admin()->create();
        $user = User::factory()->create();

        // Create overdue tasks.
        $tasksUserOverdue = Task::factory(3)
            ->overdue()
            ->create(
                [
                    'user_id' => $user->id,
                    'state' => StateEnum::InProgess,
                ]
            );

        // Create tasks that are not overdue, but have a deadline.
        $tasksUserNotOverdue = Task::factory(5)
            ->notOverdue()
            ->create(
                [
                    'user_id' => $user->id,
                    'state' => StateEnum::InProgess->value,
                ]
            );

        // Act.
        Sanctum::actingAs($adminUser, [TaskTokenEnum::Update->value]);
        $response = $this->putJson(
            route('tasks.update', $tasksUserNotOverdue->first()),
            ['state' => StateEnum::Done->value]
        );

        // Assert.
        $response->assertForbidden();

        $this->assertDatabaseHas(
            'tasks',
            [
                'id' => $tasksUserNotOverdue->first()->id,
                'state' => StateEnum::InProgess->value,
            ]
        );
    }
}
