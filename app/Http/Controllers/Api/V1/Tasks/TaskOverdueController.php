<?php

namespace App\Http\Controllers\Api\V1\Tasks;

use App\Http\Resources\V1\TaskResource;
use App\Http\Controllers\Controller;
use App\Models\Scopes\CreatorScope;
use App\Enums\Auth\Roles\Role;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskOverdueController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Assume that most overdue tasks are displayed first.
        return TaskResource::collection(
            Task::oldest('deadline')->overdue()->get()
        );
    }
}
