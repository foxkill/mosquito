<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Resources\V1\TaskResource;
use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskProjectsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Task $task)
    {
        return TaskResource::collection($task->with('project')->get());
    }
}
