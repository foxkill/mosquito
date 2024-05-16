<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\StateEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreTaskRequest;
use App\Http\Requests\V1\UpdateTaskRequest;
use App\Http\Resources\V1\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a specific task owned by the current user.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Store a newly created task in the backend.
     */
    public function store(StoreTaskRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $data = $request->validated();

        $task = $user->tasks()->create(array_merge(
            $data,
            ['state' => StateEnum::Todo->value]
        ));

        if ($request->has('project_id')) {
            $task->project()->associate($request->input('project_id'));
            $task->save();
        }

        return $task;
    }

    /**
     * Update a specific task for the current user.
     */
    public function update(Task $task, UpdateTaskRequest $request)
    {
        $result = $task->update($request->validated());

        if ($request->has('project_id')) {
            $task->refresh();
            $task->project()->associate($request->input('project_id'));
            $task->save();
        }

        return $result;
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
