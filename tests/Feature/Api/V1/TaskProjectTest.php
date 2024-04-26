<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\TaskTokenEnum;
use Laravel\Sanctum\Sanctum;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
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

        $projects =  Project::factory(3)->create();

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
        Sanctum::actingAs($user, [TaskTokenEnum::ReadProjects->value]);
        $response = $this->getJson(route('tasks.projects', $tasks[0]));

        dump($response->getContent());
        // Assert.
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(8, 'data');
    }
}
