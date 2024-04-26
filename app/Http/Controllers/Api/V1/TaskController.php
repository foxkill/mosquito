<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\StoreTaskRequest;
use App\Http\Resources\V1\TaskResource;
use App\Http\Controllers\Controller;
use App\Enums\StateEnum;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a list of tasks for an user.
     */
    public function index()
    {
        return TaskResource::collection(Task::latest()->get());
    }

    /**
     * Display a specific task owned by the current user.
     */
    public function show(Task $task)
    {
        // return new TaskResource($task->load('projects'));
        return new TaskResource($task);
    }

    /**
     * Store a newly created task in the backend.
     */
    public function store(StoreTaskRequest $request)
    {
        /** @var \App\Model\User $user */
        $user = auth()->user();

        return $user->tasks()->create(
            array_merge(
                $request->validated(),
                [
                    // Make sure the state is set to "todo" when creating a new task.
                    'state' => StateEnum::Todo,
                ]
            )
        );
    }

    /**
     * Update a specific task for the current user.
     */
    public function update(Task $task, StoreTaskRequest $request)
    {
        // What about sanetizing the input?
        return $task->update($request->validated());
    }

    /**
     * Remove a specified task for the current user.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        // Return 204 - No content.
        return response()->noContent();
    }
}
