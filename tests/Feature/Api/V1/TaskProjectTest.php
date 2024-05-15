<?php

namespace Tests\Feature\Api\V1;

use App\Enums\Auth\Token\TaskTokenEnum;
use App\Enums\StateEnum;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TaskProjectTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;

    // To use faker within this test class.
    use WithFaker;

    /**
     * It should be able to read all tasks for a project.
     */
    public function test_read_projects_for_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $projects = Project::factory(3)->create();

        $tasks = Task::factory(3)->create([
            'project_id' => $projects[0]->id,
            'user_id' => $user->id,
        ]);

        $tasksWithoutProject = Task::factory(2)->create([
            'user_id' => $user->id,
        ]);

        $tasksWithDiffrentProject = Task::factory(3)->create([
            'project_id' => $projects[2]->id,
            'user_id' => $user->id,
        ]);

        $tasksOtherUser = Task::factory(2)->create([
            'project_id' => $projects[1]->id,
            'user_id' => $otherUser->id,
        ]);

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::ReadTaskProjects->value]);
        $response = $this->getJson(route('tasks.project', $tasks[0]));

        // Assert.
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(8, 'data');

        // TODO: assert that for i. e. Task 8 has Project 3.
        // TODO assert that Task 3 and 4 have an empty project list and the project_id == 0.
    }

    /**
     * It should be a able to assign a project to a task
     * when creating the task itself.
     */
    public function test_assign_project_when_creating_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $project = Project::factory()->create();

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Create->value]);
        $response = $this->postJson(
            route('tasks.store'),
            $expectedData = [
                'title' => 'my title',
                'description' => 'a cool description',
                'state' => StateEnum::Todo,
                'deadline' => now()->addDays(10),
                'project_id' => $project->id,
            ]
        );

        // Assert.
        $response->assertCreated();

        $this->assertDatabaseHas(
            'tasks',
            $expectedData
        );
    }

    /**
     * It should not be a able to assign a project to a task
     * when creating the task itself.
     */
    public function test_assign_non_existing_project_when_creating_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Create->value]);
        $response = $this->postJson(
            route('tasks.store'),
            $expectedData = [
                'title' => 'my title',
                'description' => 'a cool description',
                'state' => StateEnum::Todo,
                'deadline' => now()->addDays(10),
                'project_id' => 666,
            ]
        );

        // Assert.
        $response->assertUnprocessable();
    }

    /**
     * It should be a able to assign/reassign a project when
     * updating a task.
     */
    public function test_assign_project_when_updating_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $projects = Project::factory(2)->create();
        $tasks = Task::factory(2)
            ->todo()
            ->notOverdue()
            ->for($user)
            ->create();

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);
        $response = $this->putJson(
            route('tasks.update', ['task' => $tasks->first()]),
            $expectedData = [
                'title' => 'my title',
                'description' => 'a cool description',
                'state' => StateEnum::InProgess->value,
                'project_id' => $projects->last()->id,
            ]
        );

        // Assert.
        $response->assertOk();

        $this->assertDatabaseHas(
            'tasks',
            $expectedData,
        );
    }

    /**
     * It should not be a able to assign/reassign a project when
     * updating a task.
     */
    public function test_assign_non_existing_project_when_updating_a_task(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $tasks = Task::factory(2)
            ->todo()
            ->notOverdue()
            ->for($user)
            ->create();

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);
        $response = $this->putJson(
            route('tasks.update', ['task' => $tasks->first()]),
            $expectedData = [
                'title' => 'my title',
                'description' => 'a cool description',
                'state' => StateEnum::InProgess->value,
                'project_id' => 666,
            ]
        );

        // Assert.
        $response->assertUnprocessable();
    }
}
