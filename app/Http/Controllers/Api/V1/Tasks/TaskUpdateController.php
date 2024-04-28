<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Requests\V1\PatchTaskRequest;
use App\Api\V1\Actions\UpdateTaskAction;
use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Task $task, PatchTaskRequest $request, UpdateTaskAction $action)
    {
        $data = $request->validated();
        return $action->execute($task, $request->validated());
    }
}
