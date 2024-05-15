<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TaskResource;
use App\Models\Task;

class TaskProjectsController extends Controller
{
    /**
     * Show projects that are associated with a task.
     */
    public function __invoke(Task $task)
    {
        return TaskResource::collection($task->with('project')->get());
    }
}
