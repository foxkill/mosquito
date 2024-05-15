<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProjectTasksResource;
use App\Models\Project;

class ProjectTasksController extends Controller
{
    /**
     * Show a project with its associacted tasks.
     */
    public function __invoke(Project $project)
    {
        return new ProjectTasksResource($project);
    }
}
