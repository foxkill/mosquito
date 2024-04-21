<?php

namespace Tests\Feature\Api\V1;

use App\Enums\StateEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
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
                'title' => $this->faker->sentence(),
                'state' => 'todo',
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
    public function test_empty_create_task_request(): void 
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
     * It should reject a wrong state.
     */
    public function test_invalid_state_for_create_task_request(): void 
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

        $theTask = [
            'user_id' => $user->id,
            'title' => 'The Task', 
            'state' => StateEnum::Done,
            'decription' => 'my description',
        ];

        $task = Task::create($theTask);
        
        // Act.
        $response = $this->actingAs($user)->putJson(
            route('tasks.update', ['task' => $task->id]),
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
