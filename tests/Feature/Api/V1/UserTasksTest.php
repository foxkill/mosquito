<?php

namespace Tests\Feature\Api\V1;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserTasksTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
    use WithFaker;

    /**
     * It should retrieve tasks for a specific user.
     */
    public function test_should_retrieve_tasks_of_a_specific_user(): void
    {
        // Arrange.
        $user = User::factory()->create();

        $tasks = Task::factory(2)->create([
            'user_id' => $user->id,
        ]);

        $otherUser = User::factory()->create();
        Task::factory(4)->create([
            'user_id' => $otherUser->id,
        ]);

        // Act.
        Sanctum::actingAs($user);

        $response = $this
            ->getJson(
                route('user.tasks', ['user' => $otherUser])
            );

        // Assert.
        $response
            ->assertStatus(Response::HTTP_OK);
            // TODO: check that there are 4 task returned.
            // TODO: implement Middleware and Token as well as rules.
            //->assertJsonCount(4, 'data.tasks');
    }
}
