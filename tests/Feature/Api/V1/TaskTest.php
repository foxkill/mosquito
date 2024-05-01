<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\TaskTokenEnum;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Arr;
use App\Enums\StateEnum;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * Maximum number of characters allowed for the title field.
     */
    const TITLE_CHARACTER_LIMIT = 255;

    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
    use WithFaker;

    /**
     * The Api should not be accessible for unauthorized users.
     */
    public function test_no_unauthorized_access(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => $this->faker->sentence(),
            'state' => StateEnum::Todo->value,
            'description' => $this->faker->realText(),
        ]);

        // Act - we do not impersonate the user.
        $response = $this->getJson(route('tasks.index'));

        // Assert - repsonse code, data count, id & title match.
        $response->assertUnauthorized();
    }

    /**
     * The api should not incidentally deliver data of other users.
     */
    public function test_should_not_be_able_to_read_task_of_other_users(): void
    {
        // Arrange.
        $taskOtherUser = Task::factory()
            ->for(User::factory()->create())
            ->create();

        // Act
        $response = $this
            ->actingAs(User::factory()->create())
            ->getJson(route('tasks.show', $taskOtherUser));

        // Assert
        $response->assertNotFound();
    }

    /**
     * The api should return a task.
     */
    public function test_should_read_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $task = Task::factory()
            ->for($user)
            ->create();

        // Act.
        $response = $this
            ->actingAs($user)
            ->getJson(route('tasks.show', $task));

        // Assert - repsonse code, data count, title, state, description match.
        $response
            ->assertOk()
            ->assertJson([
                'data' => Arr::only($task->toArray(), ['title', 'state', 'description'])
            ]);
    }

    /**
     * The api should return a list of tasks.
     */
    public function test_should_list_tasks(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $tasks = Task::factory(10)->create(
            [
                'user_id' => $user->id,
                'description' => $this->faker->realText,
            ]
        );

        $otherUser = User::factory()->create();

        Task::factory(3)
            ->create(['user_id' => $otherUser->id]);

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::List->value]);
        $response = $this->getJson(route('tasks.index'));

        // Assert - repsonse code, data count, id & title match.
        $response
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJson([
                'data' => [
                    Arr::only($tasks->toArray(), ['title', 'state', 'description'])
                ]
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
                'title' => $this->faker->sentence(),
                'state' => StateEnum::Todo->value,
                'description' => $this->faker->realText(),
            ]
        );

        // Assert.
        $response->assertCreated();
    }

    /**
     * It should correctly handle an empty request.
     */
    public function test_should_not_create_task_from_empty_data(): void
    {
        // Arrange.
        $user = User::factory()->create();

        // Act.
        $response = $this->actingAs($user)->postJson(route('tasks.store'), []);

        // Assert.
        $response->assertUnprocessable();
    }

    /**
     * It should reject an invalid input data.
     */
    public function test_should_not_create_a_task_from_invalid_data(): void
    {
        // Arrange.
        $user = User::factory()->create();

        // Act.
        $response = $this->actingAs($user)->postJson(route('tasks.store'), [
            'title' => $this->faker->word,
            'description' => $this->faker->word,
            'state' => '<script></script>',
        ]);

        // Assert.
        $response->assertUnprocessable();
    }

    /**
     * It should update a task. 
     */
    public function test_should_update_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'The Task',
            'state' => StateEnum::Done->value,
            'decription' => 'my description',
            'deadline' => $deadline = now()->addDay(),
        ]);

        // Act.
        $response = $this->actingAs($user)->putJson(
            route('tasks.update', $task),
            $expectedData = [
                'title' => $this->faker->sentence(),
                'state' => StateEnum::InProgess->value,
                'description' => $this->faker->sentence(),
            ]
        );

        // Assert that the response is successful
        $response->assertOk();

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
     * It should not be able to update a task if the max length of the title is 
     * surpassed.
     * 
     * Exam Additional Part.
     */
    public function test_should_not_be_able_to_update_a_task_if_surpassing_title_limit(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $task = Task::factory()->notOverdue()->for($user)->create();
        $title = $this->faker->sentence(256);

        // Act.
        $response = $this->actingAs($user)->putJson(
            route('tasks.update', ['task' => $task]),
            [
                'title' => $title,
                'state' => StateEnum::InProgess->value,
                'description' => $this->faker->sentence(),
            ]
        );

        // Assert.
        $response->assertUnprocessable();
    }

    /**
     * It should delete a task. 
     */
    public function test_should_delete_a_task(): void
    {
        // Arrange
        $user = User::factory()->create();

        $this->actingAs($user);

        // Create a task for the user
        $task = Task::factory()->create(['user_id' => $user->id]);

        // Act
        // Make a DELETE request to delete the task
        $response = $this->deleteJson(route('tasks.destroy', ['task' => $task]));

        // Assert that the response is successful
        $response->assertNoContent();

        // Assert that the task was deleted from the database
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
