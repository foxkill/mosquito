<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Resources\V1\TaskIndexResource;
use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskIndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return TaskIndexResource::collection(Task::latest()->get());
    }
}
