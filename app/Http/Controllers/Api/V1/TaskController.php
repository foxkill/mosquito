<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\StateEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreTaskRequest;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Task::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $user = auth()->user();

        $data = $request->safe()->only('title', 'description');

        return Task::create(
            array_merge(
                $data, 
                [
                    'user_id' => $user->id,
                    // Make sure the state is set to "todo" when creating the task.
                    'state' => StateEnum::Todo,
                ]
            )
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
