<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Requests\V1\UpdateTaskRequest;
use App\Api\V1\Actions\UpdateTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TaskResource;
use App\Models\Task;

class TaskUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Task $task, UpdateTaskRequest $request, UpdateTaskAction $action)
    {
        return $action->execute($task, $request->validated());
    }
}
