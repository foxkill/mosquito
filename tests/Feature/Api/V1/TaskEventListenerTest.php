<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Enums\Auth\Token\TaskTokenEnum;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeadlineBreachedEmail;
use Laravel\Sanctum\Sanctum;
use App\Events\TaskUpdating;
use App\Enums\StateEnum;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class TaskEventListenerTest extends TestCase
{
    // We must use this trait to create tables.
    use RefreshDatabase;
    // To use faker within this test class.
    use WithFaker;

    /**
     * It should check the task updated event listener.
     */
    public function test_should_invoke_the_task_updated_event_listener(): void
    {
        // Arrange.
        Event::fake([TaskUpdating::class]);

        $user = User::factory()->create();

        $tasks = Task::withoutEvents(function () use ($user) {
            return Task::factory(2)->create([
                'user_id' => $user->id,
                'state' => StateEnum::Todo->value,
                'deadline' => now()->subDays(1),
            ]);
        });

        // Act.
        Sanctum::actingAs($user, [TaskTokenEnum::Update->value]);

        $response = $this->putJson(
            route('tasks.update', $tasks->first()),
            ['state' => StateEnum::InProgess]
        );

        // Assert.
        $response->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $tasks->first()->id,
            'state' => StateEnum::InProgess->value
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


        // Assert
        Mail::assertSent(DeadlineBreachedEmail::class);
    }
}

