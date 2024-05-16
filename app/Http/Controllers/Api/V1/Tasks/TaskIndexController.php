<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TaskIndexResource;
use App\Models\Task;

class TaskIndexController extends Controller
{
    /**
     * List all tasks.
     */
    public function __invoke()
    {
        return TaskIndexResource::collection(Task::latest()->get());
    }
}
