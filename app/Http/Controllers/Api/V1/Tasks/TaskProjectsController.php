<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TaskResource;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskProjectsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Task $task, Request $request)
    {
        return TaskResource::collection($task->with('project')->get());
    }
}
