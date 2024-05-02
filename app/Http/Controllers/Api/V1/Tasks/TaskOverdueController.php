<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Resources\V1\TaskResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

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
