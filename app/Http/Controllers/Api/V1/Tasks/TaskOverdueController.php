<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskOverdueController extends Controller
{
    /**
     * Shows all tasks that are overdue.
     */
    public function __invoke(Request $request)
    {
        // Assume that most overdue tasks are displayed first.
        return TaskResource::collection(
            Task::oldest('deadline')->overdue()->get()
        );
    }
}
