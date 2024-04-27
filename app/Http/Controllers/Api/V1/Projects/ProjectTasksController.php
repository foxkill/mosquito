<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Resources\V1\ProjectTasksResource;
use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectTasksController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return ProjectTasksResource::collection(
            Project::with('tasks')->get()
        );
    }
}