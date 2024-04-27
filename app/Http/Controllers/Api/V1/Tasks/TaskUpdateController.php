<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Task $task, UpdateTaskRequest $request)
    {
        $data = $request->validated();
        return $task->update($data);
    }
}
