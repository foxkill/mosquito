<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProjectResource;
use App\Models\Project;

class ProjectShowController extends Controller
{
    /**
     * Show a specific project.
     */
    public function __invoke(Project $project)
    {
        return new ProjectResource($project);
    }
}
