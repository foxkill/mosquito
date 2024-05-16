<?php

namespace Tests\Feature\Api\V1;

use App\Enums\Auth\Token\TaskTokenEnum;
use App\Enums\StateEnum;
use App\Events\TaskUpdating;
use App\Mail\DeadlineBreachedEmail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskEventListenerTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;

    // To use faker within this test class.
    use WithFaker;

    /**
     * It should check the task updating event listener is invoked.
     */
    public function test_should_invoke_the_task_updating_event_listener(): void
    {
        // Arrange.
        Event::fake([TaskUpdating::class]);

        $user = User::factory()->create();

        $tasks = Task::withoutEvents(function () use ($user) {
            return Task::factory(2)
                ->overdue()
                ->create(
                    [
                        'user_id' => $user->id,
                        'state' => StateEnum::Todo->value,
                    ]
                );
        });

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);

        $response = $this->putJson(
            route('tasks.update', $tasks->first()),
            ['state' => StateEnum::InProgess->value]
        );

        // Assert.
        $response->assertForbidden();

        // The user is not an admin user and should not be able
        // to access overdue tasks.
        $this->assertDatabaseHas('tasks', [
            'id' => $tasks->first()->id,
            'state' => StateEnum::Todo->value,
        ]);

        Event::assertDispatched(TaskUpdating::class);
    }

    /**
     * It should send an email if event is invoked.
     */
    public function test_should_send_an_email_to_user_if_event_is_invoked(): void
    {
        // Arrange
        Mail::fake();

        $user = User::factory()->create();

        $tasks = Task::withoutEvents(function () use ($user) {
            return Task::factory()->create(
                [
                    'user_id' => $user->id,
                    'deadline' => now()->subDays(4),
                ]
            );
        });

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);

        $response = $this->putJson(
            route('tasks.update', $tasks->first()),
            ['state' => StateEnum::InProgess]
        );

        $response->assertForbidden();

        // Assert
        Mail::assertSent(DeadlineBreachedEmail::class);
    }
}
