<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\ProjectTokenEnum;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Arr;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
    use WithFaker;

    /**
     * Test that no one can access the route unauthorized.
     */
    public function test_no_unauthorized_project_access(): void
    {
        // Arrange.
        $project = Project::factory(10)->create(
            ['title' => $this->faker->sentence]
        );

        $user = User::factory()->create();

        Sanctum::actingAs(
            $user,
            [ProjectTokenEnum::Read->value]
        );

        // Act
        $response = $this
            ->getJson(route('projects.index'));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * The user should be able to read a project.
     */
    public function test_should_read_a_project(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $project = Project::factory()->create();

        // Act.
        Sanctum::actingAs(
            $user,
            [ProjectTokenEnum::Read->value]
        );

        $response = $this
            ->getJson(route('projects.show', $project));

        // Assert
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'data' => Arr::only($project->toArray(), ['id', 'title'])
            ]);
    }

    /**
     * The user should be able to read a project.
     */
    public function test_should_create_a_project(): void
    {
        // Arrange.
        $user = User::factory()->create();
        Project::factory()->create();

        // Act.
        Sanctum::actingAs(
            $user,
            [ProjectTokenEnum::Create->value]
        );

        $response = $this
            ->postJson(
                route('projects.store'),
                [
                    'title' => $expectedData = $this->faker->sentence(),
                ]
            );

        // Assert
        $response
            ->assertStatus(Response::HTTP_CREATED);

        // Assert that the data was actually written.
        $this->assertDatabaseHas(
            'projects',
            ['title' => $expectedData]
        );
    }

    /**
     * It should list projects.
     */
    public function test_should_list_projects(): void
    {
        // Arrange
        $user = User::factory()->create();
        Project::factory(10)->create([
            'title' => $this->faker->sentence,
        ]);

        // Act
        Sanctum::actingAs($user, [ProjectTokenEnum::List->value]);

        $response = $this->getJson(route('projects.index'));

        // Assert that the response is successful
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(
                10,
                'data'
            );
    }

    /**
     * It should update a project.
     */
    public function test_should_update_a_project(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $project = Project::create(['title' => $this->faker->sentence]);

        // Act.
        Sanctum::actingAs($user, [ProjectTokenEnum::Update->value]);

        $response = $this->putJson(
            route('projects.update', $project),
            $expectedData = [
                'title' => $expectedData = 'My new project title',
            ]
        );

        // Assert that the response is successful
        $response
            ->assertStatus(Response::HTTP_OK);

        // Assert that the data was actually written.
        $this->assertDatabaseHas(
            'projects',
            [
                'id' => $project->id,
                'title' => $expectedData
            ]
        );
    }

    /**
     * It should delete a project.
     */
    public function test_should_delete_a_project(): void
    {
        // Arrange
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'title' => $this->faker->sentence,
        ]);

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        // Act
        Sanctum::actingAs($user, [ProjectTokenEnum::Delete->value]);

        $response = $this->deleteJson(
            route(
                'projects.destroy',
                ['project' => $project]
            )
        );

        // Assert that the response is successful
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        // Assert that the project was deleted from the database
        $this->assertDatabaseMissing('projects', ['id' => $task->id]);

        // Assert that the project_id of the task is set null.
        $this->assertDatabaseHas(
            'tasks',
            [
                'id' => $task->id,
                'project_id' => null,
            ]
        );
    }

    /**
     * It should retrieve tasks of a project.
     */
    public function test_should_retrieve_tasks_of_a_project(): void
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $projects = Project::factory(2)->create([
            'title' => $this->faker->sentence,
        ]);

        $task = Task::factory(2)->create([
            'user_id' => $user->id,
            'project_id' => $projects->first()->id,
        ]);

        $taskOther = Task::factory(3)->create([
            'user_id' => $otherUser->id,
            'project_id' => $projects[1]->id,
        ]);

        // Act
        Sanctum::actingAs($user, [ProjectTokenEnum::ReadProjectTasks->value]);

        $response = $this->getJson(
            route(
                'projects.tasks',
                ['project' => $projects->first()]
            )
        );

        // Assert - that the response is successful and has the correct shape.
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data.tasks')
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'title',
                        'tasks' => [
                            '*' => [
                                'id',
                                'title',
                                'description',
                                'state',
                                'project_id',
                            ]
                        ]
                    ]
                ]
            );
    }
}
