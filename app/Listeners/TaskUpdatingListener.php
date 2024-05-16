<?php

namespace App\Listeners;

use App\Events\TaskUpdating;
use App\Mail\DeadlineBreachedEmail;
use Illuminate\Support\Facades\Mail;

class TaskUpdatingListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return mixed
     */
    public function handle(TaskUpdating $event)
    {
        // Task has no deadline or deadline is not breached.
        if ($event->task->deadline === null || now() < $event->task->deadline) {
            return;
        }

        $user = $event->task->user;
        // Log::info('Mail is sent to user because the deadline is expired');

        Mail::to($user->email)->send(
            new DeadlineBreachedEmail(
                $user->name,
                $event->task->title
            )
        );
    }
}
