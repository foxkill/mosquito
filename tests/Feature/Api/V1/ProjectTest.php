<?php

namespace Tests\Feature\Api\V1;

use App\Enums\Auth\Token\ProjectTokenEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
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
        $response = $this
            ->actingAs($user)
            ->getJson(route('projects.show', $project));

        // Assert
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                // 'data' => Arr::only($project->toArray(), ['title'])
                'data' => 'my title'
            ]);
    }
}
