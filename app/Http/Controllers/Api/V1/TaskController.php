<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\StoreTaskRequest;
use App\Http\Resources\V1\TaskCollection;
use App\Http\Resources\V1\TaskResource;
use App\Http\Controllers\Controller;
use App\Enums\StateEnum;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TaskResource::collection(Task::latest()->get());
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        /** @var \App\Model\User $user */
        $user = auth()->user();
        
        return $user->tasks()->create(
            $request->safe()->only('title', 'description') +
            [ 
                // Make sure the state is set to "todo" when creating a new task.
                'state' => StateEnum::Todo,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Task $task, StoreTaskRequest $request)
    {
        return $task->update($request->safe()->only('title', 'description', 'state'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent(); 
    }
}
