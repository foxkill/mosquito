<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TaskResource;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskOverdueController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Wanna have the most overdue tasks first?
        return TaskResource::collection(Task::latest('deadline')->overdue()->get());
    }
}
