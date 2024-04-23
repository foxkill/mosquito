<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use App\Enums\TaskTokenEnum;
use Illuminate\Support\Arr;
use App\Enums\StateEnum;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
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
        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        // Act - we do not impersonate the user.
        $response = $this->getJson(route('tasks.index'));

        // Assert - repsonse code, data count, id & title match.
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * The api should not incidentally deliver data of other users.
     */
    public function test_should_not_be_able_to_read_task_of_other_users(): void
    {
        // Arrange.
        $otherUser = User::factory()->create();
        $taskOtherUser = Task::factory()->create(['user_id' => $otherUser->id]);

        // Act
        $response = $this
            ->actingAs(User::factory()->create())
            ->getJson(route('tasks.show', $taskOtherUser));

        // Assert
        $response
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * The api should return a task.
     */
    public function test_should_read_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        // Act.
        $response = $this
            ->actingAs($user)
            ->getJson(route('tasks.show', $task));

        // Assert - repsonse code, data count, title, state, description match.
        $response
            ->assertStatus(Response::HTTP_OK)
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
        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        $otherUser = User::factory()->create();

        Task::factory()
            ->count(7)
            ->create(['user_id' => $otherUser->id]);

        // Act.
        $response = $this->actingAs($user)->getJson(route('tasks.index'));
        
        // Assert - repsonse code, data count, id & title match.
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1)
            ->assertJson([
                'data' => [
                    Arr::only($task->toArray(), ['title', 'state', 'description'])
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
                'state' => StateEnum::Todo,
                'description' => $this->faker->realText(),
            ]
        );

        // Assert.
        $response
            ->assertStatus(Response::HTTP_CREATED);
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
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * It should update a task. 
     */
    public function test_should_update_a_task(): void 
    {
        // Arrange.
        $user = User::factory()->create();

        $task = Task::create([
            'user_id' => $user->id,
            'title' => 'The Task', 
            'state' => StateEnum::Done,
            'decription' => 'my description',
        ]);
        
        // Act.
        $response = $this->actingAs($user)->putJson(
            route('tasks.update', $task),
            $expectedData = [
                'title' => $this->faker->sentence(),
                'state' => StateEnum::InProgess,
                'description' => $this->faker->sentence(),
            ]
        );

        // Assert that the response is successful
        $response
            ->assertStatus(Response::HTTP_OK);
        
        // Assert that the data was actually written.
        $this->assertDatabaseHas(
            'tasks', 
            array_merge($expectedData, ['id' => $task->id])
        );
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
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        // Assert that the task was deleted from the database
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
