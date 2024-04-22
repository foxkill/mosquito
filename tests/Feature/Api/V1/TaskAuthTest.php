<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\TaskTokenEnum;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskAuthTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;

    /**
     * It should make sure that accessing the 
     * read route with incorrect ablities is denied.
     */
    public function test_that_read_route_forbidden(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        // Act - as sanctum user.
        Sanctum::actingAs($user, [TaskTokenEnum::Delete->value]);
        $response = $this
            ->getJson(route('tasks.show', $task));

        // Assert - that access is denied.
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * It should make sure that accessing the 
     * read route is allowed.
     */
    public function test_that_read_route_is_allowed(): void
    {
        // Arrange.
        $user = User::factory()->create();
        $task = Task::factory()->create(
            ['user_id' => $user->id]
        );

        // Act - as sanctum user.
        Sanctum::actingAs($user, [TaskTokenEnum::Read->value]);
        $response = $this
            ->getJson(route('tasks.show', $task));

        // Assert - that access is denied.
        $response->assertStatus(Response::HTTP_OK);
    }
}
