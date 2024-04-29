<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\TaskTokenEnum;
use App\Enums\StateEnum;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TaskOverdueTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
    use WithFaker;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        DB::listen(function ($query) {
            Log::info($query->sql, ['bindings' => $query->bindings, 'time' => $query->time]);
        });
    }


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
                        ]
                    ]
                ]
            )
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('data', count($tasks))
                    ->where('data.0.deadline', $tasks->last()->deadline->toJson())
            );
    }

    /**
     * It should return all overdue messages.
     */
    public function test_should_list_overdue_tasks_for_an_admin(): void
    {
        // Arrange.
        $adminUser = User::factory()->admin()->create();
        $otherUser = User::factory()->create();
        $user = User::factory()->create();

        // The admin user should not have task.
        Task::factory(3)->create(
            ['user_id' => $adminUser->id]
        )->each(function ($task, $key) {
            $task->update(['deadline' => now()->subDays($key + 1)]);
        });

        // Create overdue tasks.
        $tasks = Task::factory(3)->create(
            ['user_id' => $user->id]
        )->each(function ($task, $key) {
            $task->update(['deadline' => now()->subDays($key + 1)]);
        });

        $taskNotOverdue = Task::factory(2)->create([
            'user_id' => $user->id,
            'deadline' => now()->addDays(4),
        ]);

        $tasksOtherUser = Task::factory(5)->create(
            ['user_id' => $otherUser->id]
        )->each(function ($task, $key) {
            $task->update(['deadline' => now()->subDays($key + 1)]);
        });

        $taskOtherUserNotOverdue = Task::factory(2)->create([
            'user_id' => $otherUser->id,
            'deadline' => now()->addDays(4),
        ]);

        // Act.
        Sanctum::actingAs($adminUser, [TaskTokenEnum::Read->value]);
        $response = $this->getJson(route('tasks.overdue'));

        // Assert.
        $response->assertOk()
            ->assertJsonCount(11, 'data');
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
        $tasks = Task::factory(3)->create(
            [
                'user_id' => $user->id,
                'state' => StateEnum::Todo->value,
            ]
        )->each(function ($task, $key) {
            $task->update([
                'deadline' => now()->subDays($key + 1)
            ]);
        });

        // Create tasks that are not overdue, but have a deadline.
        $tasksOtherUser = Task::factory(5)->create(
            ['user_id' => $otherUser->id]
        )->each(function ($task, $key) {
            $task->update(['deadline' => now()->addDays($key + 1)]);
        });

        // Act.
        Sanctum::actingAs($adminUser, [TaskTokenEnum::Update->value]);
        $response = $this->putJson(
            route('tasks.update', $tasks[1]),
            [
                'state' => StateEnum::InProgess,
            ]
        );

        // Assert.
        $response->assertOk();

        $this->assertDatabaseHas(
            'tasks',
            [
                'id' => $tasks[1]->id,
                'state' => StateEnum::InProgess->value,
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
        $otherUser = User::factory()->create();
        $user = User::factory()->create();

        // Create overdue tasks.
        $tasks = Task::factory(3)->create(
            [
                'user_id' => $user->id,
                'state' => StateEnum::Todo->value,
            ]
        )->each(function ($task, $key) {
            $task->update([
                'deadline' => now()->subDays($key + 1)
            ]);
        });

        // Create tasks that are not overdue, but have a deadline.
        $tasksOtherUser = Task::factory(5)->create(
            ['user_id' => $otherUser->id]
        )->each(function ($task, $key) {
            $task->update(['deadline' => now()->addDays($key + 1)]);
        });

        // Act.
        Sanctum::actingAs($adminUser, [TaskTokenEnum::Update->value]);
        $response = $this->putJson(
            route('tasks.update', $tasksOtherUser->first()),
            [
                'state' => StateEnum::InProgess,
            ]
        );

        // Assert.
        $response->assertUnauthorized();

        $this->assertDatabaseHas(
            'tasks',
            [
                'id' => $tasksOtherUser->first(),
                'state' => StateEnum::Todo->value,
            ]
        );
    }
}
